<?php

use Kirby\Http\Response;

return [
    [
        'pattern' => 'spiele',
        'action' => fn () => go(site()->homePage()->url(), 301)
    ],
    [
        'pattern' => 'feeds/(:alpha)',
        'method' => 'GET',
        'action' => function (string $type) {
            if (!in_array($type, ['rss', 'json'], true)) {
                return false;
            }

            $content = kirby()->cache('pages')->getOrSet(
                'feed-' . $type,
                function () use ($type) {
                    $items = collection('articles')->limit(10);

                    $data = [
                        'url' => site()->url(),
                        'feedurl' => url("feeds/{$type}"),
                        'title' => 'Trollspiele aus dem RPG Maker',
                        'description' => page('blog')->headerText()->value(),
                        'titlefield' => 'title',
                        'datefield' => 'date',
                        'textfield' => 'text',
                        'modified' => $items->count()
                            ? $items->first()->modified('r', 'date')
                            : site()->homePage()->modified('r', 'date'),
                        'items' => $items
                    ];

                    return trim(snippet("feed/{$type}", $data, true));
                }
            );

            $contentType = match ($type) {
                'rss' => 'application/rss+xml',
                'json' => 'application/json',
            };

            return new Response($content, $contentType);
        }
    ]
];
