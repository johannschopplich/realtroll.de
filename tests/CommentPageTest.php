<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use Kirby\Cms\Page;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Comments\CommentPage;

#[CoversClass(CommentPage::class)]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CommentPageTest extends TestCase
{
    private App $kirby;

    protected function setUp(): void
    {
        Page::$models['comment'] = CommentPage::class;

        $now = date('c');

        $this->kirby = new App([
            'roots'   => ['index' => sys_get_temp_dir() . '/rt-model-' . uniqid()],
            'options' => ['url' => 'https://realtroll.de'],
            'users'   => [
                // A user's UUID derives from its account id, so this is `user://troll`.
                [
                    'id'    => 'troll',
                    'email' => 'troll@realtroll.de',
                    'role'  => 'admin',
                    'name'  => 'real Troll',
                ],
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
                                'content'  => ['uuid' => 'article-a', 'title' => 'Artikel A'],
                                'children' => [
                                    [
                                        'slug'     => 'comment-visitor',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-visitor', 'title' => 'K', 'name' => 'Anna', 'text' => 'Erster', 'parentId' => '', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-developer',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-developer', 'title' => 'K', 'name' => 'Gespeicherter Name', 'text' => 'Antwort', 'parentId' => '', 'author' => 'user://troll', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-nameless-developer',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-nameless', 'title' => 'K', 'name' => 'Besuchername', 'text' => 'Antwort', 'parentId' => '', 'author' => 'user://nobody', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-deleted-developer',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-deleted', 'title' => 'K', 'name' => 'Alter Name', 'text' => 'Antwort', 'parentId' => '', 'author' => 'user://gone', 'date' => $now],
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
        Page::$models = [];
    }

    private function comment(string $slug): CommentPage
    {
        $comment = $this->kirby->page('blog/artikel-a/' . $slug);
        assert($comment instanceof CommentPage);

        return $comment;
    }

    #[Test]
    public function a_visitor_comment_shows_the_stored_name(): void
    {
        $comment = $this->comment('comment-visitor');

        $this->assertNull($comment->developer());
        $this->assertSame('Anna', $comment->displayName());
    }

    #[Test]
    public function a_developer_reply_shows_the_live_account_name(): void
    {
        $this->assertSame('real Troll', $this->comment('comment-developer')->displayName());
    }

    #[Test]
    public function a_nameless_developer_account_falls_back_to_the_stored_name(): void
    {
        $comment = $this->comment('comment-nameless-developer');

        // The reference still resolves (badge shows) – only the name falls back.
        $this->assertNotNull($comment->developer());
        $this->assertSame('Besuchername', $comment->displayName());
    }

    #[Test]
    public function a_deleted_developer_reference_falls_back_to_the_stored_name(): void
    {
        $comment = $this->comment('comment-deleted-developer');

        $this->assertNull($comment->developer());
        $this->assertSame('Alter Name', $comment->displayName());
    }
}
