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

    'email' => [
        'transport' => [
            'type' => 'smtp',
            'host' => 'smtp.resend.com',
            'port' => 465,
            'security' => 'ssl',
            'auth' => true,
            'username' => 'resend',
            'password' => env('RESEND_API_KEY')
        ]
    ],

    'realtroll.comments' => [
        'turnstile' => [
            'sitekey' => env('TURNSTILE_SITE_KEY'),
            'secret' => env('TURNSTILE_SECRET_KEY'),
            'hostname' => env('TURNSTILE_HOSTNAME') ?: null,
            'action' => env('TURNSTILE_ACTION') ?: null
        ]
    ],

    'johannschopplich.helpers' => [
        'robots' => [
            'enabled' => true
        ],
        'sitemap' => [
            'enabled' => true,
            'exclude' => [
                'templates' => ['comment'],
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
