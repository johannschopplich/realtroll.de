<?php

use Kirby\Cms\Url;

return [
    [
        'pattern' => [
            'player',
            'player/(:all)'
        ],
        'action' => function () {
            go('play/?' . Url::query());
        }
    ]
];
