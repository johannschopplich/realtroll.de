<?php

/**
 * @var \Kirby\Cms\Page $page
 * @var \Kirby\Cms\Page $game
 * @var string $classes additional classes for the list, e.g. `justify-center`
 * @var string $appearance `bevel` (opaque, for light surfaces) | `glass` (translucent, for dark surfaces)
 * @var string $size `base` | `lg`
 */

$classes ??= '';
$appearance ??= 'bevel';
$size ??= 'base';

$releaseStatusLabels = [
  'demo' => 'Demo',
  'full-version' => 'Vollversion',
  'in-progress' => 'In Arbeit',
];

$chips = array_filter([
  $releaseStatusLabels[$game->releaseStatus()->value()] ?? null,
  $game->published()->isNotEmpty() ? $game->published()->toDate('Y') : null,
  !$page->isHomePage() && $game->engine()->isNotEmpty() ? $game->engine()->value() : null
]);

if ($chips === []) return;
?>
<ul class="game-chips-<?= $size ?> <?= $classes ?>">
  <?php foreach ($chips as $chip): ?>
    <li class="game-chip-<?= $appearance ?>-<?= $size ?>">
      <?= esc($chip) ?>
    </li>
  <?php endforeach ?>
</ul>
