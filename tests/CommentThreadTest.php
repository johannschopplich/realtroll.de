<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Comments\CommentPage;
use RealTroll\Comments\CommentThread;

#[CoversClass(CommentThread::class)]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class CommentThreadTest extends TestCase
{
    private App $kirby;

    protected function setUp(): void
    {
        Page::$models['comment'] = CommentPage::class;

        $now = date('c');

        $this->kirby = new App([
            'roots'   => ['index' => sys_get_temp_dir() . '/rt-thread-' . uniqid()],
            'options' => ['url' => 'https://realtroll.de'],
            'site'    => [
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
                                        'content'  => ['uuid' => 'c-top', 'title' => 'K', 'name' => 'Anna', 'text' => 'Erster', 'parentId' => '', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-reply',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-reply', 'title' => 'K', 'name' => 'Ben', 'text' => 'Antwort', 'parentId' => 'page://c-top', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-orphan',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-orphan', 'title' => 'K', 'name' => 'Cid', 'text' => 'Waise', 'parentId' => 'page://ghost', 'date' => $now],
                                    ],
                                    [
                                        // The write side stores the level-2 target itself when
                                        // its ancestor is gone.
                                        'slug'     => 'comment-reply-to-orphan',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-reply-orphan', 'title' => 'K', 'name' => 'Dana', 'text' => 'Antwort an Waise', 'parentId' => 'page://c-orphan', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-reply-to-hidden',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-reply-hidden', 'title' => 'K', 'name' => 'Eve', 'text' => 'Antwort an Verborgenen', 'parentId' => 'page://c-hidden', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-cross',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-cross', 'title' => 'K', 'name' => 'Fry', 'text' => 'Quer', 'parentId' => 'page://c-foreign', 'date' => $now],
                                    ],
                                    [
                                        'slug'     => 'comment-deep-reply',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-deep-reply', 'title' => 'K', 'name' => 'Ivy', 'text' => 'Tiefe Antwort', 'parentId' => 'page://c-reply', 'date' => $now],
                                    ],
                                ],
                                'drafts' => [
                                    [
                                        'slug'     => 'comment-hidden',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-hidden', 'title' => 'K', 'name' => 'Gus', 'text' => 'Verborgen', 'parentId' => '', 'date' => $now],
                                    ],
                                ],
                            ],
                            [
                                'slug'     => 'artikel-b',
                                'template' => 'article',
                                'content'  => ['uuid' => 'article-b', 'title' => 'Artikel B'],
                                'children' => [
                                    [
                                        'slug'     => 'comment-foreign',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-foreign', 'title' => 'K', 'name' => 'Hal', 'text' => 'Fremd', 'parentId' => '', 'date' => $now],
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

    private function thread(): CommentThread
    {
        return new CommentThread($this->comments());
    }

    private function comments(): Pages
    {
        return $this->kirby->page('blog/artikel-a')->children()->template('comment')->unlisted();
    }

    private function comment(string $slug): Page
    {
        return $this->kirby->page('blog/artikel-a/' . $slug);
    }

    #[Test]
    public function stores_a_top_level_target_as_the_parent(): void
    {
        $this->assertSame('page://c-top', $this->thread()->storedParentId('page://c-top'));
    }

    #[Test]
    public function flattens_a_level_two_target_onto_its_thread_opener(): void
    {
        $this->assertSame('page://c-top', $this->thread()->storedParentId('page://c-reply'));
    }

    #[Test]
    public function anchors_to_the_target_itself_when_its_ancestor_is_gone(): void
    {
        // The target is a promoted orphan – it renders top-level, so a reply
        // must attach to it, not chase the dead ancestor reference.
        $this->assertSame('page://c-orphan', $this->thread()->storedParentId('page://c-orphan'));
    }

    #[Test]
    #[DataProvider('unresolvableReferenceProvider')]
    public function promotes_any_unresolvable_reference_to_top_level(string $reference): void
    {
        $this->assertNull($this->thread()->storedParentId($reference));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unresolvableReferenceProvider(): iterable
    {
        yield 'an empty reference' => [''];
        yield 'a user uuid' => ['user://troll'];
        yield 'a missing page' => ['page://ghost'];
        yield 'a hidden draft' => ['page://c-hidden'];
        yield 'a foreign article\'s comment' => ['page://c-foreign'];
    }

    #[Test]
    public function a_reply_nests_under_its_thread_opener(): void
    {
        $parent = $this->thread()->parentOf($this->comment('comment-reply'));

        $this->assertNotNull($parent);
        $this->assertTrue($parent->is($this->comment('comment-top')));
    }

    #[Test]
    public function a_reply_to_a_promoted_orphan_nests_under_it(): void
    {
        $thread = $this->thread();

        // The orphan itself renders top-level (its own parent is gone) …
        $this->assertNull($thread->parentOf($this->comment('comment-orphan')));

        // … so a reply stored against it must nest under it instead of
        // detaching to the bottom of the list.
        $parent = $thread->parentOf($this->comment('comment-reply-to-orphan'));

        $this->assertNotNull($parent);
        $this->assertTrue($parent->is($this->comment('comment-orphan')));
    }

    #[Test]
    public function a_reply_to_a_hidden_parent_renders_top_level(): void
    {
        // Hiding is reversible: the reply renders top-level while the parent is
        // a draft and re-nests automatically once the parent is unhidden.
        $this->assertNull($this->thread()->parentOf($this->comment('comment-reply-to-hidden')));
    }

    #[Test]
    public function a_reply_to_a_foreign_articles_comment_renders_top_level(): void
    {
        $this->assertNull($this->thread()->parentOf($this->comment('comment-cross')));
    }

    #[Test]
    public function a_reply_to_a_visible_nested_reply_renders_top_level(): void
    {
        $this->assertNull($this->thread()->parentOf($this->comment('comment-deep-reply')));
    }

    #[Test]
    public function groups_every_comment_exactly_once_in_input_order(): void
    {
        $thread = $this->thread();

        $slugs = static fn (array $comments): array => array_map(
            static fn (Page $comment): string => $comment->slug(),
            $comments
        );

        $this->assertSame(
            ['comment-top', 'comment-orphan', 'comment-reply-to-hidden', 'comment-cross', 'comment-deep-reply'],
            $slugs($thread->topLevel())
        );
        $this->assertSame(['comment-reply'], $slugs($thread->repliesTo($this->comment('comment-top'))));
        $this->assertSame(['comment-reply-to-orphan'], $slugs($thread->repliesTo($this->comment('comment-orphan'))));
        $this->assertSame([], $thread->repliesTo($this->comment('comment-reply')));
    }
}
