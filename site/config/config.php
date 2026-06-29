<?php

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Site;
use Kirby\Query\Runners\DefaultRunner;

return [

    'debug' => env('KIRBY_DEBUG', false),

    'yaml' => [
        'handler' => 'symfony'
    ],

    'query' => [
        'runner' => DefaultRunner::class
    ],

    'panel' => [
        'install' => env('KIRBY_PANEL_INSTALL', false),
        'slug' => env('KIRBY_PANEL_SLUG', 'panel'),
        'language' => 'de',
        'vue' => [
            'compiler' => false
        ]
    ],

    'cache' => [
        'pages' => [
            'active' => env('KIRBY_CACHE', false),
            'ignore' => fn (Page $page) => $page->kirby()->user() !== null
        ]
    ],

    'routes' => require __DIR__ . '/routes.php',

    'johannschopplich.helpers' => [
        'robots' => [
            'enabled' => true
        ],
        'sitemap' => [
            'enabled' => true,
            'exclude' => [
                'pages' => ['spiele']
            ]
        ],
        'meta' => [
            'defaults' => function (App $kirby, Site $site, Page $page): array {
                $jsonld = [];

                if ($page->isHomePage()) {
                    $jsonld['WebSite'] = [
                        'name' => $site->title()->value(),
                        'url' => $site->url(),
                        'inLanguage' => 'de',
                        'publisher' => [
                            '@type' => 'Person',
                            'name' => 'Johann Schopplich',
                            'url' => 'https://johannschopplich.com',
                        ],
                    ];
                }

                return ['jsonld' => $jsonld];
            }
        ]
    ]

];
