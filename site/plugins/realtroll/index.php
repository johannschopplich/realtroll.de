<?php

use Kirby\Cms\App;

App::plugin('realtroll/website', [
    'siteMethods' => [
        'realTroll' => function (): array {
            return [
                '@type' => 'Person',
                'name' => 'real Troll',
                'url' => 'https://realtroll.de',
                'sameAs' => [
                    'https://realtroll.de',
                    'https://realtroll.hpage.com',
                    'https://www.instagram.com/der_real_troll/',
                    'https://www.youtube.com/user/realtroll'
                ]
            ];
        }
    ],
    'pageMethods' => [
        'breadcrumbList' => function (): array {
            $crumbs = [$this->site()->homePage(), ...$this->parents()->flip()->values(), $this];

            $items = [];
            $position = 1;
            foreach ($crumbs as $crumb) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $crumb->title()->value(),
                    'item' => $crumb->url()
                ];
            }

            return ['itemListElement' => $items];
        }
    ]
]);
