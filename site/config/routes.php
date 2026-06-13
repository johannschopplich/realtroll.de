<?php

return [
    [
        'pattern' => 'spiele',
        'action' => fn () => go(site()->homePage()->url(), 301)
    ]
];
