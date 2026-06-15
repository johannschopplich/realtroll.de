<?php

use Kirby\Cms\Page;

class ArticlePage extends Page
{
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
            'publisher' => $this->site()->realTroll()
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
