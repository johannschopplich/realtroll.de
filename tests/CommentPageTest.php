<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;
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
                                        // Promoted orphan: its parent reference points nowhere.
                                        'slug'     => 'comment-orphan',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-orphan', 'title' => 'K', 'name' => 'Cid', 'text' => 'Waise', 'parentId' => 'page://ghost', 'date' => $now],
                                    ],
                                    [
                                        // The guard's orphan branch stores the level-2 target
                                        // itself when its ancestor is gone.
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
                                        // Parent is a visible nested reply, not a thread opener –
                                        // the third level has nowhere to nest.
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

    private function comment(string $slug): CommentPage
    {
        $comment = $this->kirby->page('blog/artikel-a/' . $slug);
        assert($comment instanceof CommentPage);

        return $comment;
    }

    private function siblings(): Pages
    {
        return $this->kirby->page('blog/artikel-a')->children()->template('comment')->unlisted();
    }

    #[Test]
    public function a_comment_without_a_parent_reference_is_top_level(): void
    {
        $this->assertTrue($this->comment('comment-top')->isTopLevel());
        $this->assertFalse($this->comment('comment-reply')->isTopLevel());
    }

    #[Test]
    public function a_reply_nests_under_its_visible_top_level_parent(): void
    {
        $parent = $this->comment('comment-reply')->topLevelParent($this->siblings());

        $this->assertNotNull($parent);
        $this->assertTrue($parent->is($this->comment('comment-top')));
    }

    #[Test]
    public function a_reply_to_a_promoted_orphan_nests_under_it(): void
    {
        // The orphan itself renders top-level (its own parent is gone) …
        $this->assertNull($this->comment('comment-orphan')->topLevelParent($this->siblings()));

        // … so a reply the guard stored against it must nest under it instead
        // of detaching to the bottom of the list.
        $parent = $this->comment('comment-reply-to-orphan')->topLevelParent($this->siblings());

        $this->assertNotNull($parent);
        $this->assertTrue($parent->is($this->comment('comment-orphan')));
    }

    #[Test]
    public function a_reply_to_a_hidden_parent_promotes_to_top_level(): void
    {
        // Hiding is reversible: the reply renders top-level while the parent is
        // a draft and re-nests automatically once the parent is unhidden.
        $this->assertNull($this->comment('comment-reply-to-hidden')->topLevelParent($this->siblings()));
    }

    #[Test]
    public function a_reply_to_a_foreign_articles_comment_promotes_to_top_level(): void
    {
        $this->assertNull($this->comment('comment-cross')->topLevelParent($this->siblings()));
    }

    #[Test]
    public function a_reply_to_a_visible_nested_reply_promotes_to_top_level(): void
    {
        // Flattening stops at two levels. When the parent is itself a nested
        // reply whose own parent is still visible, the third level can't nest
        // and renders at the root instead.
        $this->assertNull($this->comment('comment-deep-reply')->topLevelParent($this->siblings()));
    }
}
