<?php

$items = page('spiele')?->children()->listed();
if (!$items || $items->count() === 0) return;

?>
<ul class="grid md:grid-cols-2 md:[&>li:nth-child(even)>*]:mr-[-2px]">
  <?php $i = 0 ?>
  <?php foreach ($items as $game): ?>
    <?php
    $isFeatured = $i === 0;
    $logo = $game->logo()->toFile();
    $screenshots = $game->screenshots()->map(fn ($i) => $i->url())->values();
    ?>

    <li
      class="group mt-[-2px] bg-primary-700 <?= $isFeatured ? 'md:col-span-2' : '' ?>"
      <?= attr(['data-screenshots' => implode('|', $screenshots), 'data-title' => $game->title()], ' ') ?>
    >
      <?php if ($isFeatured): ?>
        <?php /* z-0 → group-hover:z-2 lifts the card above the header faces (z-1) */ ?>
        <div class="relative z-0 p-3xl bg-white border-2 border-primary-700 transition-[transform,z-index] duration-200 md:grid md:grid-cols-2 md:items-center md:gap-5xl md:p-5xl md:group-hover:translate-[-4px] md:group-hover:z-2 md:group-active:translate-0">
          <?php if ($logo): ?>
            <figure class="flex items-center justify-center mb-4xl md:mb-0">
              <img
                class="pixelated max-w-full h-auto"
                src="<?= $logo->url() ?>"
                width="<?= $logo->width() * 2 ?>"
                height="<?= $logo->height() * 2 ?>"
                alt="<?= $game->title()->escape() ?>"
              >
            </figure>
          <?php endif ?>

          <a href="<?= $game->url() ?>" class="absolute inset-0" aria-hidden="true" tabindex="-1"></a>

          <div class="prose hyphenate">
            <?php snippet('components/game-chips', ['game' => $game]) ?>
            <p class="mb-lg text-balance font-stretch-normal"><?= $game->description() ?></p>
            <a
              href="<?= $game->url() ?>"
              class="link-primary"
              aria-label="<?= $game->title()->escape() ?> ansehen"
            >
              <span class="link-default [--un-decoration-color:transparent] group-hover:decoration-current">Zum Spiel</span>
              <span class="i-dinkie-icons-white-right-backhand-index absolute left-full top-1/2 ml-1 opacity-0 -translate-y-1/2 transition-[opacity,margin] duration-200 group-hover:opacity-100 group-hover:ml-2 motion-reduce:transition-none" aria-hidden="true"></span>
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="relative grid grid-rows-[1fr_auto] p-3xl h-full bg-white border-2 border-primary-700 transition-transform duration-200 md:p-5xl md:group-hover:translate-[-4px] md:group-active:translate-0">
          <?php if ($logo): ?>
            <figure class="mb-4xl">
              <img
                class="pixelated mx-auto"
                src="<?= $logo->url() ?>"
                width="<?= $logo->width() * 2 ?>"
                height="<?= $logo->height() * 2 ?>"
                alt="<?= $game->title()->escape() ?>"
              >
            </figure>
          <?php else: ?>
            <p class="flex items-center justify-center">(Kein Logo)</p>
          <?php endif ?>

          <a href="<?= $game->url() ?>" class="absolute inset-0" aria-hidden="true" tabindex="-1"></a>

          <div class="prose hyphenate">
            <p class="mb-lg text-balance font-stretch-normal"><?= $game->description() ?></p>

            <a
              href="<?= $game->url() ?>"
              class="link-primary"
              aria-label="<?= $game->title()->escape() ?> ansehen"
            >
              <span class="link-default [--un-decoration-color:transparent] group-hover:decoration-current">Zum Spiel</span>
              <?php /* Hand erscheint erst beim Hover und "läuft" Richtung Spiel; absolut positioniert → kein Layout-Shift */ ?>
              <span class="i-dinkie-icons-white-right-backhand-index absolute left-full top-1/2 ml-1 opacity-0 -translate-y-1/2 transition-[opacity,margin] duration-200 group-hover:opacity-100 group-hover:ml-3 motion-reduce:transition-none" aria-hidden="true"></span>
            </a>
          </div>
        </div>
      <?php endif ?>
    </li>
    <?php $i++ ?>
  <?php endforeach ?>
</ul>
