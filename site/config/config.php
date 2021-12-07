<?php

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
            'ignore' => fn (\Kirby\Cms\Page $page) => kirby()->user() !== null
        ]
    ],

    'kirby-extended' => [
        'robots' => [
            'enable' => true
        ],
        'sitemap' => [
            'enable' => true
        ]
    ]

];
