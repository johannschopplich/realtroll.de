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

#[CoversClass(ArticlePage::class)]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ArticlePageTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        require_once dirname(__DIR__) . '/site/models/article.php';
        Page::$models['comment'] = CommentPage::class;
        Page::$models['article'] = ArticlePage::class;

        $this->app = new App([
            'roots' => ['index' => sys_get_temp_dir() . '/rt-article-' . uniqid()],
            'site'  => [
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
                                        'slug'     => 'comment-own',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-own', 'title' => 'K', 'name' => 'Anna', 'text' => 'Eigener', 'parentId' => ''],
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
                                        'content'  => ['uuid' => 'c-foreign', 'title' => 'K', 'name' => 'Ben', 'text' => 'Fremder', 'parentId' => ''],
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

    private function article(): ArticlePage
    {
        $article = $this->app->page('blog/artikel-a');
        assert($article instanceof ArticlePage);

        return $article;
    }

    #[Test]
    public function comment_finds_an_own_comment_by_its_slug(): void
    {
        $this->assertSame('blog/artikel-a/comment-own', $this->article()->comment('comment-own')?->id());
    }

    #[Test]
    public function comment_returns_null_for_a_uuid_shortcut_to_another_articles_comment(): void
    {
        $this->assertNull($this->article()->comment('@c-foreign'));
    }
}
