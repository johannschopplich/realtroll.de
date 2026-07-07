<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use Kirby\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Comments\SubmissionGuards;
use RealTroll\Comments\Turnstile;

#[CoversClass(SubmissionGuards::class)]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SubmissionGuardsTest extends TestCase
{
    private App $kirby;

    protected function setUp(): void
    {
        $now = date('c');
        $old = date('c', time() - 3600);

        $this->kirby = new App([
            'roots'   => ['index' => sys_get_temp_dir() . '/rt-guards-' . uniqid()],
            'options' => ['url' => 'https://realtroll.de'],
            'users'   => [
                // A user's UUID derives from its account id, so this is `user://troll`.
                [
                    'id'    => 'troll',
                    'email' => 'troll@realtroll.de',
                    'role'  => 'admin',
                    'name'  => 'real Troll',
                ],
                // A logged-in account with no display name: the trusted branch keys
                // off a non-empty name, so this one is treated as a visitor.
                [
                    'id'    => 'nobody',
                    'email' => 'nobody@realtroll.de',
                    'role'  => 'admin',
                ],
            ],
            'site' => [
                'children' => [
                    [
                        'slug'     => 'blog',
                        'children' => [
                            [
                                'slug'     => 'artikel-a',
                                'template' => 'article',
                                'content'  => ['uuid' => 'article-a', 'title' => 'Artikel A', 'commentsEnabled' => 'true'],
                                'children' => [
                                    [
                                        'slug'     => 'comment-top',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-top', 'title' => 'K', 'name' => 'Anna', 'text' => 'Erster', 'parentId' => '', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-level2',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-level2', 'title' => 'K', 'name' => 'Ben', 'text' => 'Antwort', 'parentId' => 'page://c-top', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-level2-orphan',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-level2-orphan', 'title' => 'K', 'name' => 'Cid', 'text' => 'Waise', 'parentId' => 'page://ghost', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-dupe',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-dupe', 'title' => 'K', 'name' => 'Dana', 'text' => 'Doppelter Kommentar', 'parentId' => '', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-old',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-old', 'title' => 'K', 'name' => 'Eve', 'text' => 'Alter Kommentar', 'parentId' => '', 'date' => $old],
                                    ],
                                    [
                                        // Empty text with a recent date: the duplicate scan
                                        // must skip it, not fatal on the null field value.
                                        'slug'     => 'comment-empty-text',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-empty-text', 'title' => 'K', 'name' => 'Gap', 'text' => '', 'parentId' => '', 'date' => $now],
                                    ],
                                ],
                            ],
                            [
                                'slug'     => 'artikel-b',
                                'template' => 'article',
                                'content'  => ['uuid' => 'article-b', 'title' => 'Artikel B', 'commentsEnabled' => 'true'],
                                'children' => [
                                    [
                                        'slug'     => 'comment-foreign',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-foreign', 'title' => 'K', 'name' => 'Fry', 'text' => 'Fremd', 'parentId' => '', 'date' => $now],
                                    ],
                                ],
                            ],
                            [
                                'slug'     => 'artikel-gesperrt',
                                'template' => 'article',
                                'content'  => ['uuid' => 'article-locked', 'title' => 'Gesperrt', 'commentsEnabled' => 'false'],
                            ],
                            [
                                // Legacy article without the toggle field – absent means enabled.
                                'slug'     => 'artikel-ohne-feld',
                                'template' => 'article',
                                'content'  => ['uuid' => 'article-nofield', 'title' => 'Ohne Feld'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    protected function tearDown(): void
    {
        App::destroy();
    }

    private function guards(bool $turnstileOk = true): SubmissionGuards
    {
        return new SubmissionGuards(
            new Turnstile('secret', static fn (): array => ['success' => $turnstileOk])
        );
    }

    private function request(array $body = []): Request
    {
        $token = $this->kirby->csrf();

        return new Request([
            'method' => 'POST',
            'body'   => array_merge([
                'pageUuid'              => 'page://article-a',
                'name'                  => 'Klaus',
                'text'                  => 'Ein netter Kommentar.',
                'parentId'              => '',
                'csrf'                  => $token,
                'cf-turnstile-response' => 'valid-token',
                SubmissionGuards::HONEYPOT_FIELD => '',
            ], $body),
        ]);
    }

    #[Test]
    public function accepts_a_valid_visitor_submission_without_author(): void
    {
        $verdict = $this->guards()->evaluate($this->request());

        $this->assertTrue($verdict->accepted);
        $this->assertNull($verdict->author);
        $this->assertNull($verdict->parentId);
        // The verdict carries everything the write needs – the route must never
        // re-resolve the article or re-clean the values (a second copy drifts).
        $this->assertTrue($verdict->article->is($this->kirby->page('blog/artikel-a')));
        $this->assertSame('Klaus', $verdict->name);
        $this->assertSame('Ein netter Kommentar.', $verdict->text);
    }

    #[Test]
    public function cleaned_values_ride_the_verdict(): void
    {
        // Zero-width characters are stripped before storage; the verdict must
        // carry the cleaned form, not the raw body.
        $verdict = $this->guards()->evaluate($this->request(['name' => "Kla\u{200B}us"]));

        $this->assertTrue($verdict->accepted);
        $this->assertSame('Klaus', $verdict->name);
    }

    #[Test]
    public function rejects_when_the_toggle_is_explicitly_false(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['pageUuid' => 'page://article-locked']));

        $this->assertFalse($verdict->accepted);
    }

    #[Test]
    public function accepts_when_the_toggle_field_is_absent(): void
    {
        // Legacy articles have no `commentsEnabled` field; absent means enabled.
        $verdict = $this->guards()->evaluate($this->request(['pageUuid' => 'page://article-nofield']));

        $this->assertTrue($verdict->accepted);
    }

    #[Test]
    public function rejects_when_the_target_is_not_an_article(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['pageUuid' => 'page://c-top']));

        $this->assertFalse($verdict->accepted);
    }

    #[Test]
    public function rejects_when_the_target_page_is_missing(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['pageUuid' => '']));

        $this->assertFalse($verdict->accepted);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function badCsrfProvider(): iterable
    {
        yield 'a tampered token' => ['tampered'];
        yield 'a missing token' => [''];
    }

    #[Test]
    #[DataProvider('badCsrfProvider')]
    public function rejects_a_bad_csrf_token(string $csrf): void
    {
        // Fail-closed on both a tampered and an absent token. The client keys its
        // transparent retry on this stable code, not the German copy.
        $verdict = $this->guards()->evaluate($this->request(['csrf' => $csrf]));

        $this->assertFalse($verdict->accepted);
        $this->assertSame('csrf', $verdict->code);
    }

    /**
     * Name and text rejections share one V::invalid pass. The whitespace and
     * zero-width cases also prove clean() runs before the length check – the
     * value collapses to empty and fails `required`, never stored as blank.
     *
     * @return iterable<string, array{array<string, string>, string}>
     */
    public static function invalidFieldProvider(): iterable
    {
        yield 'a missing name' => [['name' => ''], 'name'];
        yield 'an overlong name' => [['name' => str_repeat('a', 61)], 'name'];
        yield 'a whitespace-only name' => [['name' => "  \t  "], 'name'];
        yield 'a missing text' => [['text' => ''], 'text'];
        yield 'an overlong text' => [['text' => str_repeat('a', 4001)], 'text'];
        yield 'a whitespace-only text' => [['text' => "  \n\t  "], 'text'];
        yield 'a zero-width-only text' => [['text' => "\u{200B}\u{FEFF}\u{2060}"], 'text'];
    }

    #[Test]
    #[DataProvider('invalidFieldProvider')]
    public function rejects_invalid_field_input(array $body, string $field): void
    {
        $verdict = $this->guards()->evaluate($this->request($body));

        $this->assertFalse($verdict->accepted);
        $this->assertSame($field, $verdict->field);
        $this->assertSame('validation', $verdict->code);
    }

    #[Test]
    public function rejects_a_filled_honeypot(): void
    {
        $verdict = $this->guards()->evaluate(
            $this->request([SubmissionGuards::HONEYPOT_FIELD => 'http://spam.example'])
        );

        $this->assertFalse($verdict->accepted);
    }

    #[Test]
    public function rejects_an_exact_duplicate_within_the_window(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['text' => 'Doppelter Kommentar']));

        $this->assertFalse($verdict->accepted);
    }

    #[Test]
    public function accepts_a_repeat_outside_the_window(): void
    {
        // Same text as a comment posted an hour ago – outside the dedupe window.
        $verdict = $this->guards()->evaluate($this->request(['text' => 'Alter Kommentar']));

        $this->assertTrue($verdict->accepted);
    }

    #[Test]
    public function accepts_when_an_existing_comment_has_empty_text(): void
    {
        // The fixture holds a recent comment whose text field is empty (a
        // hand-edited content file); under strict_types the scan must skip it
        // instead of raising a TypeError and killing every later submission.
        $verdict = $this->guards()->evaluate($this->request());

        $this->assertTrue($verdict->accepted);
    }

    #[Test]
    public function rejects_a_failed_turnstile(): void
    {
        $verdict = $this->guards(turnstileOk: false)->evaluate($this->request());

        $this->assertFalse($verdict->accepted);
        // The client keys the widget reset on this stable code, not the copy.
        $this->assertSame('turnstile', $verdict->code);
    }

    #[Test]
    public function operator_submission_is_accepted_with_author_and_skips_bot_defenses(): void
    {
        $this->kirby->impersonate('troll@realtroll.de');

        // Failing Turnstile, filled honeypot and a duplicate text – all skipped
        // for the trusted operator.
        $verdict = $this->guards(turnstileOk: false)->evaluate($this->request([
            'text'                           => 'Doppelter Kommentar',
            SubmissionGuards::HONEYPOT_FIELD => 'filled',
        ]));

        $this->assertTrue($verdict->accepted);
        $this->assertSame('user://troll', $verdict->author);
    }

    #[Test]
    public function operator_still_needs_a_valid_csrf_token(): void
    {
        $this->kirby->impersonate('troll@realtroll.de');

        $verdict = $this->guards()->evaluate($this->request(['csrf' => 'tampered']));

        $this->assertFalse($verdict->accepted);
    }

    #[Test]
    public function nameless_account_is_subject_to_the_bot_defenses(): void
    {
        // A named operator skips the bot defenses; a nameless account must not.
        $this->kirby->impersonate('nobody@realtroll.de');

        $verdict = $this->guards(turnstileOk: false)->evaluate($this->request());

        $this->assertFalse($verdict->accepted);
        $this->assertSame('turnstile', $verdict->code);
    }

    #[Test]
    public function nameless_account_is_accepted_without_an_author(): void
    {
        // A nameless account gets no author, so its comment renders no badge.
        $this->kirby->impersonate('nobody@realtroll.de');

        $verdict = $this->guards()->evaluate($this->request());

        $this->assertTrue($verdict->accepted);
        $this->assertNull($verdict->author);
    }

    #[Test]
    public function visitor_cannot_forge_an_author_via_the_post_body(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['author' => 'user://troll']));

        $this->assertTrue($verdict->accepted);
        $this->assertNull($verdict->author);
    }

    #[Test]
    public function resolves_a_reply_to_a_top_level_comment(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['parentId' => 'page://c-top']));

        $this->assertTrue($verdict->accepted);
        $this->assertSame('page://c-top', $verdict->parentId);
    }

    #[Test]
    public function rejects_a_reply_to_a_non_comment_page(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['parentId' => 'page://article-b']));

        $this->assertFalse($verdict->accepted);
    }

    #[Test]
    public function rejects_a_reply_to_a_file_or_user_uuid(): void
    {
        $this->assertFalse($this->guards()->evaluate($this->request(['parentId' => 'user://troll']))->accepted);
        $this->assertFalse($this->guards()->evaluate($this->request(['parentId' => 'file://whatever']))->accepted);
    }

    #[Test]
    public function rejects_a_reply_to_a_comment_of_a_foreign_article(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['parentId' => 'page://c-foreign']));

        $this->assertFalse($verdict->accepted);
    }

    #[Test]
    public function promotes_a_reply_to_a_missing_parent_to_top_level(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['parentId' => 'page://ghost']));

        $this->assertTrue($verdict->accepted);
        $this->assertNull($verdict->parentId);
    }

    #[Test]
    public function flattens_a_reply_to_a_level_two_comment_onto_its_ancestor(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['parentId' => 'page://c-level2']));

        $this->assertTrue($verdict->accepted);
        $this->assertSame('page://c-top', $verdict->parentId);
    }

    #[Test]
    public function flattens_onto_the_target_when_the_level_two_ancestor_is_gone(): void
    {
        $verdict = $this->guards()->evaluate($this->request(['parentId' => 'page://c-level2-orphan']));

        $this->assertTrue($verdict->accepted);
        $this->assertSame('page://c-level2-orphan', $verdict->parentId);
    }
}
