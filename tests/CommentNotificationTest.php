<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Email\PHPMailer;
use Kirby\Exception\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Comments\CommentNotification;
use RealTroll\Comments\CommentPage;

if (function_exists('env') === false) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}

final class MailSpy
{
    public static array|null $props = null;
    public static Throwable|null $exception = null;
}

#[CoversClass(CommentNotification::class)]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CommentNotificationTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        MailSpy::$props = null;
        MailSpy::$exception = null;
        $_SERVER['COMMENTS_FROM']      = 'kommentare@realtroll.de';
        $_SERVER['COMMENTS_NOTIFY_TO'] = 'ops@yahoo.example';

        require_once dirname(__DIR__) . '/site/models/article.php';
        Page::$models['comment'] = CommentPage::class;
        Page::$models['article'] = ArticlePage::class;

        $now = date('c');

        $this->app = new App([
            'roots' => [
                'index'     => sys_get_temp_dir() . '/rt-mail-' . uniqid(),
                'templates' => dirname(__DIR__) . '/site/templates',
            ],
            'options'    => ['url' => 'https://realtroll.de'],
            'users'      => [
                // A user's UUID derives from its account id, so this is `user://troll`.
                [
                    'id'    => 'troll',
                    'email' => 'troll@realtroll.de',
                    'role'  => 'admin',
                    'name'  => 'real Troll',
                ],
            ],
            'components' => [
                'email' => static function (App $kirby, array $props, bool $debug = false): PHPMailer {
                    MailSpy::$props = $props;

                    if (MailSpy::$exception !== null) {
                        throw MailSpy::$exception;
                    }

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
                                    [
                                        // Developer parent whose account was renamed after posting:
                                        // the stored name and the live account name diverge.
                                        'slug'     => 'comment-dev',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-dev', 'title' => 'K', 'name' => 'Veralteter Name', 'text' => 'Eine Entwickler-Antwort.', 'parentId' => '', 'author' => 'user://troll', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-reply-to-dev',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-reply-dev', 'title' => 'K', 'name' => 'Ben', 'text' => 'Frage an den Entwickler.', 'parentId' => 'page://c-dev', 'date' => $now],
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
        if (file_exists($this->logFile())) {
            unlink($this->logFile());
        }

        App::destroy();
        Page::$models = [];
    }

    private function comment(string $slug): Kirby\Cms\Page
    {
        return $this->app->page('blog/artikel-a/' . $slug);
    }

    private function logFile(): string
    {
        return $this->app->root('logs') . '/comments.log';
    }

    #[Test]
    public function builds_the_subject_from_name_and_article_title(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $this->assertSame('Neuer Kommentar von Anna zu Artikel A', MailSpy::$props['subject']);
    }

    #[Test]
    public function renders_a_plain_text_alternative_part(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $text = MailSpy::$props['body']['text'];

        $this->assertStringContainsString('Der erste Kommentar zum Artikel.', $text);
        $this->assertStringContainsString('Im Panel moderieren:', $text);
    }

    #[Test]
    public function escapes_the_name_in_the_html_body(): void
    {
        CommentNotification::send($this->comment('comment-troll'));

        $html = MailSpy::$props['body']['html'];

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    #[Test]
    public function includes_the_excerpt_the_moderation_label_and_the_view_link(): void
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
    public function the_reply_line_names_the_parent_as_the_site_renders_it(): void
    {
        // The badge on the site shows the developer's live account name, so the
        // reply line must too – not the stale name stored at posting time.
        CommentNotification::send($this->comment('comment-reply-to-dev'));

        $html = MailSpy::$props['body']['html'];

        $this->assertStringContainsString('real Troll', $html);
        $this->assertStringNotContainsString('Veralteter Name', $html);
    }

    #[Test]
    public function omits_the_reply_line_for_a_top_level_comment(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $this->assertStringNotContainsString('Antwort auf', MailSpy::$props['body']['html']);
    }

    #[Test]
    public function logs_a_failed_send_with_the_reason_the_transport_gives(): void
    {
        MailSpy::$exception = new Exception(message: 'Resend rejected the message (HTTP 403): domain not verified');

        CommentNotification::send($this->comment('comment-top'));

        $this->assertStringContainsString(
            'Notification-Mail fehlgeschlagen: Resend rejected the message (HTTP 403): domain not verified',
            file_get_contents($this->logFile())
        );
    }

    #[Test]
    public function writes_no_log_for_a_delivered_notification(): void
    {
        CommentNotification::send($this->comment('comment-top'));

        $this->assertFileDoesNotExist($this->logFile());
    }
}
