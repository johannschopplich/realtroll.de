<?php snippet('header') ?>

<div class="due-container-lg text-center">
  <h1 class="editorial-title"><?= $page->title()->escape() ?></h1>
</div>

<div class="py-5xl">
  <div class="due-container max-w-prose">
    <div class="prose">
      <?= $page->text()->toBlocks() ?>
    </div>
  </div>
</div>

<?php snippet('footer') ?>
