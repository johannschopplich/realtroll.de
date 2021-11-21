<?php snippet('header') ?>

<div class="due-container-lg text-center">
  <h1 class="editorial-title"><?= $page->title()->html() ?></h1>
</div>

<div class="due-py-xl">
  <div class="due-container max-w-prose">
    <div class="content">
      <?= $page->text()->toBlocks() ?>
    </div>
  </div>
</div>

<?php snippet('footer') ?>
