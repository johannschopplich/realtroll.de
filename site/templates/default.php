<?php snippet('layout', slots: true) ?>

<div class="content-lg text-center">
  <h1 class="editorial-title"><?= $page->title()->escape() ?></h1>
</div>

<div class="py-5xl">
  <div class="content max-w-prose">
    <div class="prose">
      <?= $page->text()->toBlocks() ?>
    </div>
  </div>
</div>

<?php endsnippet() ?>
