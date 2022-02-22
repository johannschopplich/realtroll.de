<?php snippet('header') ?>

<div class="due-container-lg text-center">
  <h1 class="editorial-title hyphenated mb-5xl">
    <?= $page->title()->escape() ?>
  </h1>

  <div class="columns items-center justify-center gap-lg">
    <?php if ($page->gameFolder()->isNotEmpty()): ?>
      <div class="column-narrow">
        <a href="/play/?game=<?= $page->gameFolder() ?>" class="due-button-primary" target="_blank">
          Online spielen!
        </a>
      </div>
    <?php endif ?>
    <div class="column-narrow">
      <a href="<?= $page->downloadLink() ?>" class="<?php $page->gameFolder()->isNotEmpty() ? 'due-button-primary-outlined' : 'due-button-primary' ?>">
        Download (Windows)
      </a>
    </div>
  </div>
</div>

<div class="py-5xl">
  <div class="due-container max-w-prose">
    <div class="prose">
      <?= $page->intro()->toBlocks() ?>
    </div>
  </div>
</div>

<div id="screenshots" class="py-5xl">
  <div class="due-container-lg">
    <div class="grid grid-cols-minmax-320px gap-lg">
      <?php foreach ($page->screenshots()->toFiles() as $file): ?>
        <figure class="text-center">
          <img class="mx-auto" src="<?= $file->url() ?>" alt="<?= $file->caption()->or($file->alt()) ?>">
          <?php if ($file->caption()->isNotEmpty()): ?>
            <figcaption class="my-2 text-xs">
              <p><?= $file->caption() ?></p>
            </figcaption>
          <?php endif ?>
        </figure>
      <?php endforeach ?>
    </div>
  </div>
</div>

<?php $text = $page->text()->toBlocks() ?>
<?php if ($text->isNotEmpty()): ?>
  <div class="py-5xl">
    <div class="due-container max-w-prose">
      <div class="prose">
        <?= $text ?>
      </div>
    </div>
  </div>
<?php endif ?>

<div class="py-5xl">
  <div class="text-center">
    <a href="<?= url() ?>" class="due-button-primary">
      Zurück zur Spieleliste
    </a>
  </div>
</div>

<?php snippet('footer') ?>
