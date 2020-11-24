<?php snippet('header') ?>

<div class="container is-lg text-center">
  <h1 class="editorial-title hyphenated mb-xl">
    <?= $page->title()->html() ?>
  </h1>

  <div class="columns is-centered has-gap-m">
    <?php if ($page->gameFolder()->isNotEmpty()): ?>
      <div class="column is-narrow">
        <a href="/player/?game=<?= $page->gameFolder() ?>" class="button is-primary is-m" target="_blank">
          Online spielen!
        </a>
      </div>
    <?php endif ?>
    <div class="column is-narrow">
      <a href="<?= $page->downloadLink() ?>" class="button is-primary is-m<?php e($page->gameFolder()->isNotEmpty(), ' is-outlined') ?>">
        Download (Windows)
      </a>
    </div>
  </div>
</div>

<div class="section">
  <div class="container for-content">
    <div class="content">
      <?= $page->intro()->blocks() ?>
    </div>
  </div>
</div>

<div id="screenshots" class="section">
  <div class="container is-lg">
    <div class="game-images">
      <?php foreach ($page->screenshots()->toFiles() as $file): ?>
        <figure>
          <img src="<?= $file->url() ?>" alt="<?= $file->caption()->or($file->alt())->html() ?>">
          <?php if ($file->caption()->isNotEmpty()): ?>
            <figcaption class="mt-xs">
              <p class="text-s"><?= $file->caption()->html() ?></p>
            </figcaption>
          <?php endif ?>
        </figure>
      <?php endforeach ?>
    </div>
  </div>
</div>

<div class="section">
  <div class="container for-content">
    <div class="content">
      <?= $page->text()->blocks() ?>
    </div>
  </div>
</div>

<div class="section">
  <div class="text-center">
    <a href="<?= url() ?>" class="button is-primary">
      Zurück zur Spieleliste
    </a>
  </div>
</div>

<?php snippet('footer') ?>
