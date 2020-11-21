<?php

use Kirby\Cms\Url;

return function ($page, $site) {
    $title = $page->customTitle()->or($page->title() . ' – ' . $site->title())->value();
    $description = $page->description()->or($site->description())->value();
    $thumbnail = (function () use ($page, $site) {
        $file = $page->thumbnail()->toFile() ?? $site->thumbnail()->toFile();
        return $file ? $file->resize(1280)->url() : '/img/android-chrome-512x512.png';
    })();

    return [
        'title' => $title,
        'meta' => [
            'description' => $description,
            'theme-color' => '#462610'
        ],
        'link' => [
            'canonical' => $page->url(),
            'icon' => [
                ['href' => '/assets/img/icons/favicon-32x32.png', 'sizes' => '32x32', 'type' =>'image/png'],
                ['href' => '/assets/img/icons/favicon-16x16.png', 'sizes' => '16x16', 'type' =>'image/png']
            ]
        ],
        'og' => [
            'type' => 'website',
            'url' => $page->url(),
            'title' => $title,
            'description' => $description,
            'image' => $thumbnail
        ],
        'twitter' => [
            'card' => 'summary_large_image',
            'url' => $page->url(),
            'domain' => Url::host(),
            'title' => $title,
            'description' => $description,
            'image' => $thumbnail
        ]
    ];
};
