<?php snippet('layout', slots: true) ?>

<div class="content-lg text-center">
  <h1 class="editorial-title hyphenate mb-5xl">
    <?= $page->title()->escape() ?>
  </h1>

  <div class="columns gap-lg items-center justify-center">
    <?php if ($page->gameFolder()->isNotEmpty()): ?>
      <div class="column-narrow">
        <a href="/play/?game=<?= $page->gameFolder() ?>" class="button-primary" target="_blank">
          Online spielen!
        </a>
      </div>
    <?php endif ?>
    <div class="column-narrow">
      <a href="<?= $page->downloadLink() ?>" class="<?= $page->gameFolder()->isNotEmpty() ? 'button-primary-outlined' : 'button-primary' ?>">
        Download (Windows)
      </a>
    </div>
  </div>
</div>

<div class="py-5xl">
  <div class="content max-w-prose">
    <div class="prose">
      <?= $page->intro()->toBlocks() ?>
    </div>
  </div>
</div>

<div id="screenshots" class="py-5xl">
  <div class="content-lg">
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
    <div class="content max-w-prose">
      <div class="prose">
        <?= $text ?>
      </div>
    </div>
  </div>
<?php endif ?>

<div class="py-5xl">
  <div class="text-center">
    <a href="<?= url() ?>" class="button-primary">
      Zurück zur Spieleliste
    </a>
  </div>
</div>

<?php endsnippet() ?>
