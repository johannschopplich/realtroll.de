<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Http\Request;
use Kirby\Toolkit\V;
use Kirby\Uuid\PageUuid;
use Kirby\Uuid\Uuid;
use Throwable;

/**
 * The admission policy for a comment submission, expressed as a pure
 * `Request → Verdict` function with no HTTP, write or mail side effect.
 */
final class SubmissionGuards
{
    // Opaque, non-semantic name: a field called `website`/`url`/`email` is a
    // browser autofill target and would false-positive real visitors. This one
    // is off-screen and `autocomplete="off"`, so only a naive bot fills it.
    public const HONEYPOT_FIELD = 'hp_referrer';

    /** Window in seconds for the exact-duplicate flood brake. */
    private const DUPLICATE_WINDOW = 600;

    public function __construct(private readonly Turnstile|null $turnstile = null)
    {
    }

    public function evaluate(Request $request): Verdict
    {
        $kirby = App::instance();
        $body  = $request->body();

        $article = $this->resolveArticle((string)$body->get('pageUuid'));
        if ($article === null) {
            return Verdict::reject('form', 'article', 'Zu diesem Artikel sind keine Kommentare möglich.');
        }

        if ($this->guardEnabled($kirby, 'toggle')) {
            if (!$article->commentsEnabled()->toBool(true)) {
                return Verdict::reject('form', 'disabled', 'Für diesen Artikel sind Kommentare derzeit deaktiviert.');
            }
        }

        // CSRF – fail-closed against the session token; applies to the
        // logged-in operator too (guards against forced cross-site POSTs).
        if ($this->guardEnabled($kirby, 'csrf') && $kirby->csrf((string)$body->get('csrf')) !== true) {
            return Verdict::reject('form', 'csrf', 'Die Sitzung ist abgelaufen. Bitte lade die Seite neu und versuche es erneut.');
        }

        // Field validation – on the Unicode-cleaned values, so lengths are
        // measured after zero-width/Bidi stripping.
        $name = CommentRenderer::clean((string)$body->get('name'));
        $text = CommentRenderer::clean((string)$body->get('text'));

        if ($this->guardEnabled($kirby, 'validation')) {
            $errors = V::invalid(
                ['name' => $name, 'text' => $text],
                [
                    'name' => ['required' => true, 'maxLength' => 60],
                    'text' => ['required' => true, 'minLength' => 2, 'maxLength' => 4000],
                ],
                [
                    'name' => [
                        'Bitte gib einen Namen an.',
                        'Der Name ist zu lang (maximal 60 Zeichen).',
                    ],
                    'text' => [
                        'Bitte gib einen Kommentar ein.',
                        'Der Kommentar ist zu kurz.',
                        'Der Kommentar ist zu lang (maximal 4000 Zeichen).',
                    ],
                ]
            );

            if ($errors !== []) {
                $field   = array_key_first($errors);
                $message = is_array($errors[$field]) ? $errors[$field][0] : $errors[$field];
                return Verdict::reject($field, 'validation', $message);
            }
        }

        // Reply target resolution – UI-independent, applies to both branches.
        // A bad reference is a hard reject; an unresolvable one promotes to top-level.
        $parentResult = $this->resolveParentId($article, (string)$body->get('parentId'));
        if ($parentResult instanceof Verdict) {
            return $parentResult;
        }
        $parentId = $parentResult;

        // Trusted-operator branch – gated on a display name so it agrees with the
        // frontend, which only shows operator UI to a named user. Without the gate,
        // a nameless account gets the visitor form but a silently trusted write.
        $user = $kirby->user();
        if ($user !== null && $user->name()->isNotEmpty()) {
            return Verdict::accept($article, $name, $text, $user->uuid()->toString(), $parentId);
        }

        // Visitor defenses, cheapest first, most expensive outbound call last.
        if ($this->guardEnabled($kirby, 'honeypot') && trim((string)$body->get(self::HONEYPOT_FIELD)) !== '') {
            return Verdict::reject('form', 'honeypot', 'Deine Anfrage konnte nicht verarbeitet werden.');
        }

        if ($this->guardEnabled($kirby, 'duplicate') && $this->isDuplicate($article, $text)) {
            return Verdict::reject('form', 'duplicate', 'Diesen Kommentar hast du gerade eben schon abgeschickt.');
        }

        if ($this->guardEnabled($kirby, 'turnstile')) {
            $turnstile = $this->turnstile ?? $this->defaultTurnstile($kirby);
            if (!$turnstile->verify((string)$body->get('cf-turnstile-response'), $kirby->visitor()->ip())) {
                return Verdict::reject('form', 'turnstile', 'Die Sicherheitsprüfung ist fehlgeschlagen. Bitte versuche es erneut.');
            }
        }

        return Verdict::accept($article, $name, $text, null, $parentId);
    }

    /**
     * Resolves the requested reply target to a stored `parentId`, capping the
     * thread at two levels.
     */
    private function resolveParentId(Page $article, string $parentId): Verdict|string|null
    {
        if ($parentId === '') {
            return null;
        }

        try {
            $uuid = Uuid::for($parentId);
        } catch (Throwable) {
            $uuid = null;
        }

        // Only a page UUID can name a comment; other schemes and malformed
        // references are rejected outright.
        if (!($uuid instanceof PageUuid)) {
            return $this->rejectParent();
        }

        $target = $uuid->model();

        // Deleted/missing target → promote the reply to top-level (never lose it).
        if ($target === null) {
            return null;
        }

        if (!($target instanceof Page) || $target->intendedTemplate()->name() !== 'comment') {
            return $this->rejectParent();
        }

        // Cross-article guard: a foreign article's comment is a valid comment page,
        // so it must be rejected explicitly rather than accepted as top-level.
        if ($target->parent()?->id() !== $article->id()) {
            return $this->rejectParent();
        }

        // Read the content field explicitly – `$target->parentId()` would hit
        // Kirby's native Page::parentId() (the storage parent id), not this field.
        $targetParentId = (string)$target->content()->get('parentId');

        if ($targetParentId === '') {
            return $target->uuid()->toString();
        }

        // Target is level-2 → flatten onto its top-level ancestor, unless that
        // ancestor is itself gone, then point at the target (a promoted orphan).
        try {
            $ancestor = Uuid::for($targetParentId)?->model();
        } catch (Throwable) {
            $ancestor = null;
        }

        return $ancestor !== null ? $targetParentId : $target->uuid()->toString();
    }

    private function rejectParent(): Verdict
    {
        return Verdict::reject('form', 'parent', 'Die Antwort bezieht sich auf einen ungültigen Kommentar.');
    }

    private function defaultTurnstile(App $kirby): Turnstile
    {
        return new Turnstile(
            (string)$kirby->option('realtroll.comments.turnstile.secret', ''),
            null,
            $kirby->option('realtroll.comments.turnstile.hostname'),
            $kirby->option('realtroll.comments.turnstile.action')
        );
    }

    private function isDuplicate(Page $article, string $text): bool
    {
        $hash      = $this->textHash($text);
        $threshold = time() - self::DUPLICATE_WINDOW;

        foreach ($article->children()->template('comment') as $comment) {
            $date = $comment->date()->toDate();
            if ($date === null || $date < $threshold) {
                continue;
            }

            // Cast: a hand-edited/empty text field yields null and must not
            // fatal the whole scan under strict_types.
            if ($this->textHash((string)$comment->text()->value()) === $hash) {
                return true;
            }
        }

        return false;
    }

    private function textHash(string $text): string
    {
        $text = trim($text);

        return md5(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    private function resolveArticle(string $pageUuid): Page|null
    {
        if ($pageUuid === '') {
            return null;
        }

        try {
            $model = Uuid::for($pageUuid)?->model();
        } catch (Throwable) {
            return null;
        }

        if ($model instanceof Page && $model->intendedTemplate()->name() === 'article') {
            return $model;
        }

        return null;
    }

    private function guardEnabled(App $kirby, string $name): bool
    {
        return $kirby->option('realtroll.comments.guards.' . $name, true) !== false;
    }
}
