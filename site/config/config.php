<?php

use Kirby\Cms\Page;

return [

    'debug' => env('KIRBY_DEBUG', false),

    'panel' => [
        'install' => env('KIRBY_PANEL_INSTALL', false),
        'slug' => env('KIRBY_PANEL_SLUG', 'panel'),
        'language' => 'de'
    ],

    'cache' => [
        'pages' => [
            'active' => env('KIRBY_CACHE', false),
            'ignore' => fn (Page $page) => $page->kirby()->user() !== null
        ]
    ],

    'kql' => [
        'auth' => 'bearer'
    ],

    'headless' => [
        'token' => env('KIRBY_HEADLESS_API_TOKEN'),

        'cors' => [
            'allowOrigin' => env('KIRBY_HEADLESS_ALLOW_ORIGIN', '*')
        ]
    ],

    'johannschopplich.helpers' => [
        'robots' => [
            'enabled' => true
        ],
        'sitemap' => [
            'enabled' => true
        ]
    ]

];
