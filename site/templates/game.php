<?php snippet('header') ?>

<div class="container is-lg text-center">
  <h1 class="editorial-title hyphenated"><?= $page->title()->html() ?></h1>
</div>

<div class="section">
  <div class="container for-content">
    <div class="content mb-xl">
      <?= $page->text()->blocks() ?>
    </div>

    <div class="text-center">
      <a href="<?= $page->downloadLink() ?>" class="button is-primary is-outlined is-l">
        Downloadlink her!
      </a>
    </div>
  </div>
</div>

<div class="section">
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
  <div class="text-center">
    <a href="<?= url() ?>" class="button is-primary">
      Zurück zur Spieleliste
    </a>
  </div>
</div>

<?php snippet('footer') ?>
