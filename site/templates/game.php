<?php snippet('header') ?>

<div class="due-container-lg text-center">
  <h1 class="editorial-title hyphenated due-mb-xl">
    <?= $page->title()->escape() ?>
  </h1>

  <div class="columns items-center justify-center gap-5">
    <?php if ($page->gameFolder()->isNotEmpty()): ?>
      <div class="column-narrow">
        <a href="/play/?game=<?= $page->gameFolder() ?>" class="due-button-primary" target="_blank">
          Online spielen!
        </a>
      </div>
    <?php endif ?>
    <div class="column-narrow">
      <a href="<?= $page->downloadLink() ?>" class="due-button-primary<?php e($page->gameFolder()->isNotEmpty(), '-outlined') ?>">
        Download (Windows)
      </a>
    </div>
  </div>
</div>

<div class="due-py-xl">
  <div class="due-container max-w-prose">
    <div class="content">
      <?= $page->intro()->toBlocks() ?>
    </div>
  </div>
</div>

<div id="screenshots" class="due-py-xl">
  <div class="due-container-lg">
    <div class="game-screenshots">
      <?php foreach ($page->screenshots()->toFiles() as $file): ?>
        <figure class="text-center">
          <img class="mx-auto" src="<?= $file->url() ?>" alt="<?= $file->caption()->or($file->alt()) ?>">
          <?php if ($file->caption()->isNotEmpty()): ?>
            <figcaption class="due-mt-xs due-text-7 leading-normal">
              <p class="due-text-s"><?= $file->caption() ?></p>
            </figcaption>
          <?php endif ?>
        </figure>
      <?php endforeach ?>
    </div>
  </div>
</div>

<?php $text = $page->text()->toBlocks() ?>
<?php if ($text->isNotEmpty()): ?>
  <div class="due-py-xl">
    <div class="due-container max-w-prose">
      <div class="content">
        <?= $text ?>
      </div>
    </div>
  </div>
<?php endif ?>

<div class="due-py-xl">
  <div class="text-center">
    <a href="<?= url() ?>" class="due-button-primary">
      Zurück zur Spieleliste
    </a>
  </div>
</div>

<?php snippet('footer') ?>
