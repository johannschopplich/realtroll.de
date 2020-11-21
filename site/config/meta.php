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
            'theme-color' => '#462610',
            'apple-mobile-web-app-capable' => 'yes',
            'apple-mobile-web-app-status-bar-style' => 'default',
            'apple-mobile-web-app-title' => $site->title()->value()
        ],
        'link' => [
            'canonical' => $page->url(),
            'apple-touch-icon' => ['href' => '/img/apple-touch-icon.png', 'sizes' => '180x180'],
            'icon' => ['href' => '/img/favicon.svg', 'type' =>'image/svg+xml']
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
