<?php

use JohannSchopplich\EasyRpg\GameIndex;
use Kirby\Cms\App;
use Kirby\Data\Json;
use Kirby\Filesystem\F;
use Kirby\Http\Response;

F::loadClasses([
    'JohannSchopplich\\EasyRpg\\GameIndex' => __DIR__ . '/classes/GameIndex.php'
]);

App::plugin('johannschopplich/easyrpg', [
    'options' => [
        'cache' => true
    ],
    'routes' => [
        [
            'pattern' => 'play/games/(:any)/index.json',
            'action' => function (string $gameFolder) {
                $kirby = App::instance();
                $gameRoot = $kirby->root('index') . '/play/games/' . $gameFolder;

                if (
                    preg_match('/^[a-zA-Z0-9_-]+$/', $gameFolder) !== 1 ||
                    is_dir($gameRoot) === false
                ) {
                    $this->next();
                }

                $json = $kirby
                    ->cache('johannschopplich.easyrpg')
                    ->getOrSet($gameFolder, fn () => Json::encode(GameIndex::generate($gameRoot)));

                return Response::json($json);
            }
        ]
    ]
]);
