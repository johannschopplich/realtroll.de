<?php if ($games = page('spiele')): ?>
  <ul class="grid md:grid-cols-2">
    <?php foreach ($games->children()->listed() as $game): ?>
      <?php $screenshots = $game->screenshots()->toFiles()->map(fn ($i) => $i->url())->values() ?>

      <li class="bg-primary-700 mt-[-2px] md:[&:nth-child(2n+1)>*]:mr-[-2px]"<?= attr(['data-screenshots' => implode('|', $screenshots)], ' ') ?>>
        <div class="relative grid grid-rows-[1fr_auto] h-full bg-white border-2 border-primary-700 p-3xl transition-transform duration-200 md:p-5xl md:hover:translate-[-4px] md:hover:active:translate-0">
          <?php if ($logo = $game->logo()->toFile()): ?>
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

          <div class="prose hyphenate">
            <p class="mb-lg expanded"><?= $game->description() ?></p>

            <a href="<?= $game->url() ?>" class="button-primary">
              <span class="absolute inset-0" aria-hidden="true"></span>
              Mehr zum Spiel…
            </a>
          </div>
        </div>
      </li>
    <?php endforeach ?>
  </ul>
<?php endif ?>
