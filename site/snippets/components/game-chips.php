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

$chips = [];

if ($status = $releaseStatusLabels[$game->releaseStatus()->value()] ?? null) {
  $chips[] = ['label' => $status];
}
if ($game->published()->isNotEmpty()) {
  $chips[] = ['label' => $game->published()->toDate('Y')];
}
if (!$page->isHomePage() && $game->engine()->isNotEmpty()) {
  $chips[] = ['label' => $game->engine()->value(), 'icon' => 'i-dinkie-icons-wrench-filled'];
}

if ($chips === []) return;
?>
<ul class="chip-row-<?= $size ?> ps-0 list-none <?= $classes ?>">
  <?php foreach ($chips as $chip): ?>
    <li class="chip-<?= $appearance ?>-<?= $size ?><?php e($chip['icon'] ?? null, ' inline-flex items-center gap-2') ?>">
      <?php if ($chip['icon'] ?? null): ?>
        <span class="<?= $chip['icon'] ?> translate-y-[-1px]" aria-hidden="true"></span>
      <?php endif ?>
      <?= esc($chip['label']) ?>
    </li>
  <?php endforeach ?>
</ul>
