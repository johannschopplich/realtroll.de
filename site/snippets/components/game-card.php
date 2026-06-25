<?php

/**
 * @var \Kirby\Cms\Page $game
 * @var bool $isFeatured
 */

$isFeatured ??= false;
$logo = $game->logo()->toFile();
$screenshots = $game->screenshots()->map(fn ($i) => $i->url())->values();

?>
<li
  class="group mt-[-2px] bg-primary-700 <?php e($isFeatured, 'md:col-span-2') ?>"
  <?= attr(['data-screenshots' => implode('|', $screenshots), 'data-title' => $game->title()], ' ') ?>
>
  <?php /* Featured card lifts above the header faces: z-0 → md:group-hover:z-2 (faces are z-1) */ ?>
  <div
    class="
      relative <?php e($isFeatured, 'z-0 flex flex-col', 'grid grid-rows-[1fr_auto] h-full') ?>
      p-3xl
      bg-white border-2 border-primary-700
      <?php e($isFeatured, 'transition-[transform,z-index]', 'transition-transform') ?>
      md:p-5xl
      md:group-hover:translate-[-4px] <?php e($isFeatured, 'md:group-hover:z-2 ') ?> md:group-active:translate-0
    "
  >
    <a href="<?= $game->url() ?>" class="absolute inset-0" aria-hidden="true" tabindex="-1"></a>

    <?php if ($isFeatured): ?><div class="md:grid md:grid-cols-2 md:items-center md:gap-5xl"><?php endif ?>

      <?php if ($logo): ?>
        <figure class="<?php e($isFeatured, 'flex items-center justify-center mb-4xl md:mb-0', 'mb-4xl') ?>">
          <?php snippet('components/pixel-image', [
            'file' => $logo,
            'scale' => 2,
            'class' => $isFeatured ? 'max-w-full h-auto' : 'mx-auto',
            'alt' => $game->title()
          ]) ?>
        </figure>
      <?php elseif (!$isFeatured): ?>
        <p class="flex items-center justify-center">(Kein Logo)</p>
      <?php endif ?>

      <div class="prose hyphenate">
        <?php if ($isFeatured): ?>
          <?php snippet('components/game-chips', ['game' => $game]) ?>
        <?php endif ?>
        <p class="mb-lg text-balance font-stretch-normal"><?= $game->description() ?></p>
        <?php snippet('components/game-detail-link', ['game' => $game]) ?>
      </div>

    <?php if ($isFeatured): ?></div><?php endif ?>

    <?php snippet('components/game-play-cta', ['game' => $game]) ?>
  </div>
</li>
