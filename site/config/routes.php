<?php

use Kirby\Http\Response;

return [
    [
        'pattern' => 'spiele',
        'action' => fn () => go(site()->homePage()->url(), 301)
    ],
    // Serve the RSS feed's XSLT stylesheet so browsers render the feed as a
    // human-friendly page. The `.xsl` suffix keeps it clear of the `(:alpha)`
    // feed route below.
    [
        'pattern' => 'feeds/rss.xsl',
        'method'  => 'GET',
        'action'  => fn () => new Response(
            snippet('feed/rss.xsl', [], true),
            'text/xml'
        )
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
                'rss' => 'application/xml',
                'json' => 'application/json',
            };

            return new Response($content, $contentType);
        }
    ]
];
