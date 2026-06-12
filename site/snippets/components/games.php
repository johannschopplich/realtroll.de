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
    $screenshots = $game->screenshots()->toFiles()->map(fn ($i) => $i->url())->values();
    ?>

    <li
      class="group bg-primary-700 mt-[-2px] <?= $isFeatured ? 'md:col-span-2' : '' ?>"
      <?= attr(['data-screenshots' => implode('|', $screenshots)], ' ') ?>
    >
      <?php if ($isFeatured): ?>
        <?php /* z-0 → group-hover:z-2 lifts the card above the header faces (z-1) */ ?>
        <div class="relative z-0 bg-white border-2 border-primary-700 p-3xl md:p-5xl md:grid md:grid-cols-2 md:gap-5xl md:items-center transition-[transform,z-index] duration-200 md:group-hover:translate-[-4px] md:group-hover:z-2 md:group-active:translate-0">
          <?php if ($logo): ?>
            <figure class="mb-4xl md:mb-0 flex items-center justify-center">
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
            <p class="mb-lg font-stretch-expanded"><?= $game->description() ?></p>
            <a href="<?= $game->url() ?>" class="button-primary">
              Mehr zum Spiel…
            </a>
          </div>
        </div>
      <?php else: ?>
        <div class="relative grid grid-rows-[1fr_auto] h-full bg-white border-2 border-primary-700 p-3xl transition-transform duration-200 md:p-5xl md:group-hover:translate-[-4px] md:group-active:translate-0">
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
            <p class="mb-lg font-stretch-expanded"><?= $game->description() ?></p>

            <a href="<?= $game->url() ?>" class="button-primary">
              Mehr zum Spiel…
            </a>
          </div>
        </div>
      <?php endif ?>
    </li>
    <?php $i++ ?>
  <?php endforeach ?>
</ul>
