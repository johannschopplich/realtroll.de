<?php if ($gamesPage = page('spiele')): ?>
  <ul class="games-grid">
    <?php foreach ($gamesPage->children()->listed() as $game): ?>
      <?php
      $screenshots = array_values($game->screenshots()->toFiles()->map(fn($i) => $i->url())->data());
      ?>

      <li class="game-card"<?= attr(['data-screenshots' => implode('|', $screenshots) ?? null], ' ') ?>>
        <div class="position-relative game-card-inner">
          <?php if ($logo = $game->logo()->toFile()): ?>
            <figure class="game-logo text-center mb-l">
              <img
                class="pixelated"
                src="<?= $logo->url() ?>"
                width="<?= $logo->width() * 2 ?>"
                height="<?= $logo->height() * 2 ?>"
                alt=""
              >
            </figure>
          <?php else: ?>
            <p class="centered-content">(Kein Logo)</p>
          <?php endif ?>

          <div class="content">
            <p class="text-5 mb-m"><?= $game->description() ?></p>

            <a href="<?= $game->url() ?>" class="button is-primary text-5 stretched-link">
              Mehr zum Spiel…
            </a>
          </div>
        </div>
      </li>
    <?php endforeach ?>
  </ul>
<?php endif ?>
