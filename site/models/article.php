<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;

class ArticlePage extends Page
{
    private Pages|null $comments = null;

    public function comments(): Pages
    {
        return $this->comments ??= $this->children()->template('comment')->unlisted();
    }

    public function acceptsComments(): bool
    {
        return $this->commentsEnabled()->toBool(true);
    }

    public function metadata(): array
    {
        $description = $this->text()->toBlocks()->excerpt(140);

        $blogPosting = [
            'headline' => $this->title()->value(),
            'description' => $description,
            'url' => $this->url(),
            'inLanguage' => 'de',
            'datePublished' => $this->date()->toDate('Y-m-d'),
            'dateModified' => $this->modified('Y-m-d'),
            'author' => $this->site()->realTroll(),
            'publisher' => $this->site()->realTroll(),
            // No commenter names in the JSON-LD – just the visible count
            'commentCount' => $this->comments()->count()
        ];

        return [
            'description' => $description,
            'jsonld' => [
                'BlogPosting' => $blogPosting,
                'BreadcrumbList' => $this->breadcrumbList()
            ]
        ];
    }
}
