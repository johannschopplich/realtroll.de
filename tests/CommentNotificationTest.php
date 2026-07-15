<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use Kirby\Email\PHPMailer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Comments\CommentNotification;

if (function_exists('env') === false) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}

final class MailSpy
{
    public static array|null $props = null;
}

#[CoversClass(CommentNotification::class)]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CommentNotificationTest extends TestCase
{
    private App $kirby;

    protected function setUp(): void
    {
        MailSpy::$props = null;
        $_SERVER['COMMENTS_FROM']      = 'kommentare@realtroll.de';
        $_SERVER['COMMENTS_NOTIFY_TO'] = 'ops@yahoo.example';

        $now = date('c');

        $this->kirby = new App([
            'roots' => [
                'index'     => sys_get_temp_dir() . '/rt-mail-' . uniqid(),
                'templates' => dirname(__DIR__) . '/site/templates',
            ],
            'options'    => ['url' => 'https://realtroll.de'],
            'components' => [
                'email' => static function (App $kirby, array $props, bool $debug = false): PHPMailer {
                    MailSpy::$props = $props;
                    // Debug mode builds the message without transmitting it.
                    return new PHPMailer($props, true);
                },
            ],
            'site' => [
                'children' => [
                    [
                        'slug'     => 'blog',
                        'children' => [
                            [
                                'slug'     => 'artikel-a',
                                'template' => 'article',
                                'content'  => ['uuid' => 'article-a', 'title' => 'Artikel A'],
                                'children' => [
                                    [
                                        'slug'     => 'comment-top',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-top', 'title' => 'K', 'name' => 'Anna', 'text' => 'Der erste Kommentar zum Artikel.', 'parentId' => '', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-troll',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-troll', 'title' => 'K', 'name' => '<script>alert(1)</script>', 'text' => 'Ein ganz normaler Kommentar.', 'parentId' => '', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-reply',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-reply', 'title' => 'K', 'name' => 'Ben', 'text' => 'Meine Antwort darauf.', 'parentId' => 'page://c-top', 'date' => $now],
                                    ],
                                ],
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

    private function comment(string $slug): Kirby\Cms\Page
    {
        return $this->kirby->page('blog/artikel-a/' . $slug);
    }

    #[Test]
    public function builds_the_subject_from_name_and_article_title(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $this->assertSame('Neuer Kommentar von Anna zu Artikel A', MailSpy::$props['subject']);
    }

    #[Test]
    public function renders_both_multipart_parts(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $this->assertArrayHasKey('html', MailSpy::$props['body']);
        $this->assertArrayHasKey('text', MailSpy::$props['body']);
    }

    #[Test]
    public function escapes_the_name_in_the_html_body(): void
    {
        // The name is a stored-XSS sink: a script payload must survive only as
        // escaped text, never as a live tag.
        CommentNotification::send($this->comment('comment-troll'));

        $html = MailSpy::$props['body']['html'];

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    #[Test]
    public function includes_the_preview_and_moderation_links(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $html = MailSpy::$props['body']['html'];

        $this->assertStringContainsString('Der erste Kommentar zum Artikel.', $html);
        $this->assertStringContainsString('Im Panel moderieren', $html);
        $this->assertStringContainsString('/panel/kommentar/article-a/comment-top', $html);
    }

    #[Test]
    public function renders_a_reply_line_when_the_parent_resolves(): void
    {
        CommentNotification::send($this->comment('comment-reply'));

        $html = MailSpy::$props['body']['html'];

        $this->assertStringContainsString('Antwort auf', $html);
        $this->assertStringContainsString('Anna', $html);
        $this->assertStringContainsString('Der erste Kommentar', $html);
    }

    #[Test]
    public function omits_the_reply_line_for_a_top_level_comment(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $this->assertStringNotContainsString('Antwort auf', MailSpy::$props['body']['html']);
    }
}
