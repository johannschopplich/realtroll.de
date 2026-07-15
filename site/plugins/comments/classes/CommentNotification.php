<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;
use Throwable;

/**
 * The single per-comment notification – the load-bearing moderation trigger.
 */
final class CommentNotification
{
    private const PREVIEW_CHARS = 200;
    private const PARENT_EXCERPT_CHARS = 120;

    public static function send(Page $comment): void
    {
        $kirby = $comment->kirby();

        try {
            $article = $comment->parent();

            // The name is a stored-XSS sink: the templates esc() it, but it is
            // also collapsed to a single line here so the subject header can
            // never carry an injected newline.
            $name    = self::singleLine((string)$comment->name()->value());
            $preview = Str::excerpt((string)$comment->text()->value(), self::PREVIEW_CHARS);

            // moderateUrl → the comment's Panel page (edit/hide/delete).
            // viewUrl → a Panel bounce that redirects to the frontend anchor
            // only after login, so the author lands on the thread with a live
            // session and his reply carries the developer badge.
            $moderateUrl = $comment->panel()->url();
            $viewUrl     = $kirby->url('panel') . '/kommentar/' . $article->uuid()->id() . '/' . $comment->slug();

            [$parentName, $parentExcerpt] = self::resolveParent($comment);

            $kirby->email([
                'template' => 'comment-notification',
                'from'     => env('COMMENTS_FROM'),
                'fromName' => 'realtroll.de Kommentare',
                'to'       => env('COMMENTS_NOTIFY_TO'),
                'subject'  => sprintf('Neuer Kommentar von %s zu %s', $name, $article->title()->value()),
                'data'     => [
                    'comment'       => $comment,
                    'article'       => $article,
                    'name'          => $name,
                    'preview'       => $preview,
                    'moderateUrl'   => $moderateUrl,
                    'viewUrl'       => $viewUrl,
                    'parentName'    => $parentName,
                    'parentExcerpt' => $parentExcerpt,
                ],
            ]);
        } catch (Throwable $exception) {
            self::log($kirby, $exception);
        }
    }

    /**
     * Resolves the stored reply parent to a display name and short excerpt so a
     * reply is judgeable without opening the thread. Reads the content field
     * explicitly – `$comment->parentId()` would hit Kirby's native
     * Page::parentId() (the storage parent), not this field. An unresolvable
     * reference degrades to a top-level notification (no reply line).
     *
     * Same bounded lookup idiom as the guards: scan the article's comments
     * instead of `Uuid::for()->model()`, which crawls the whole site tree on
     * a cache miss (a parent hidden or deleted before the mail sends). A
     * hidden parent thus degrades too, matching what the site renders.
     *
     * @return array{0: string|null, 1: string|null}
     */
    private static function resolveParent(Page $comment): array
    {
        $parentId = (string)$comment->content()->get('parentId');

        if ($parentId === '') {
            return [null, null];
        }

        foreach ($comment->parent()->children()->template('comment') as $sibling) {
            if ($sibling->uuid()->toString() !== $parentId) {
                continue;
            }

            // Casts: a hand-edited comment with an empty name/text yields null,
            // which must degrade the reply line, not kill the whole notification.
            return [
                self::singleLine((string)$sibling->name()->value()),
                Str::excerpt((string)$sibling->text()->value(), self::PARENT_EXCERPT_CHARS),
            ];
        }

        return [null, null];
    }

    private static function singleLine(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }

    /**
     * Logs only a genuine send failure – a delivered mail that Yahoo later
     * spam-folders is not one, and logs nothing.
     */
    private static function log(App $kirby, Throwable $exception): void
    {
        try {
            F::append(
                $kirby->root('logs') . '/comments.log',
                sprintf("[%s] Notification-Mail fehlgeschlagen: %s\n", date('c'), $exception->getMessage())
            );
        } catch (Throwable) {
            // Logging is best-effort; never let it surface.
        }
    }
}
