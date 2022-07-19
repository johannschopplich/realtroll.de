<?php if ($gamesPage = page('spiele')): ?>
  <ul class="grid sm:grid-cols-2">
    <?php foreach ($gamesPage->children()->listed() as $game): ?>
      <?php
      $screenshots = $game->screenshots()->toFiles()->map(fn($i) => $i->url())->values();
      ?>

      <li class="game-card"<?= attr(['data-screenshots' => implode('|', $screenshots) ?? null], ' ') ?>>
        <div class="relative game-card-inner">
          <?php if ($logo = $game->logo()->toFile()): ?>
            <figure class="game-logo mb-4xl">
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

          <div class="prose hyphenated">
            <p class="mb-lg md:text-lg"><?= $game->description() ?></p>

            <a href="<?= $game->url() ?>" class="button-primary md:text-size-lg">
              <span class="absolute inset-0" aria-hidden="true"></span>
              Mehr zum Spiel…
            </a>
          </div>
        </div>
      </li>
    <?php endforeach ?>
  </ul>
<?php endif ?>
