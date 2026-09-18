<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ArticlePage::class)]
#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ArticlePageTest extends KirbyTestCase
{
    private App $app;

    protected function setUp(): void
    {
        $this->app = self::bootApp([
            'site' => [
                'children' => [
                    [
                        'slug'     => 'blog',
                        'children' => [
                            [
                                'slug'     => 'artikel-a',
                                'template' => 'article',
                                'children' => [
                                    ['slug' => 'comment-own', 'template' => 'comment'],
                                ],
                            ],
                            [
                                'slug'     => 'artikel-b',
                                'template' => 'article',
                                'children' => [
                                    [
                                        'slug'     => 'comment-foreign',
                                        'template' => 'comment',
                                        'content'  => ['uuid' => 'c-foreign'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function article(): ArticlePage
    {
        $article = $this->app->page('blog/artikel-a');
        assert($article instanceof ArticlePage);

        return $article;
    }

    #[Test]
    public function comment_finds_a_comment_by_its_slug(): void
    {
        $this->assertSame('blog/artikel-a/comment-own', $this->article()->comment('comment-own')?->id());
    }

    #[Test]
    public function comment_returns_null_for_a_uuid_shortcut_to_another_articles_comment(): void
    {
        $this->assertNull($this->article()->comment('@c-foreign'));
    }
}
