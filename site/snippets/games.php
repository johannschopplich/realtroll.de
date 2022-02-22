<?php if ($gamesPage = page('spiele')): ?>
  <ul class="grid sm:grid-cols-2">
    <?php foreach ($gamesPage->children()->listed() as $game): ?>
      <?php
      $screenshots = array_values($game->screenshots()->toFiles()->map(fn($i) => $i->url())->data());
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
            <p class="md:text-lg mb-lg"><?= $game->description() ?></p>

            <a href="<?= $game->url() ?>" class="due-button-primary md:font-size-lg">
              <span class="absolute inset-0" aria-hidden="true"></span>
              Mehr zum Spiel…
            </a>
          </div>
        </div>
      </li>
    <?php endforeach ?>
  </ul>
<?php endif ?>
