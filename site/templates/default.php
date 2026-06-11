<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true)

?>

<div class="content-lg text-center">
  <h1 class="editorial-title"><?= $page->title()->escape() ?></h1>
</div>

<div class="py-5xl">
  <div class="content-prose">
    <div class="prose">
      <?= $page->text()->toBlocks() ?>
    </div>
  </div>
</div>

<?php if ($parent = $page->parent()): ?>
  <?php snippet('components/section-divider') ?>
  <div class="text-center">
    <a href="<?= $parent->url() ?>" class="button-primary">
      Zurück zu <?= $parent->title()->escape() ?>
    </a>
  </div>
<?php endif ?>

<?php endsnippet() ?>
