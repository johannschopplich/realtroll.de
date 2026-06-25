<?php

$items = page('spiele')?->children()->listed();
if (!$items || $items->count() === 0) return;

$firstGame = $items->first();

?>
<ul class="grid md:grid-cols-2 md:[&>li:nth-child(even)>*]:mr-[-2px]">
  <?php foreach ($items as $game): ?>
    <?php snippet('components/game-card', ['game' => $game, 'isFeatured' => $game->is($firstGame)]) ?>
  <?php endforeach ?>
</ul>
