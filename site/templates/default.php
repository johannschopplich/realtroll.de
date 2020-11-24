<?php snippet('header') ?>

<div class="container is-lg text-center">
  <h1 class="editorial-title"><?= $page->title()->html() ?></h1>
</div>

<div class="section">
  <div class="container for-content">
    <div class="content">
      <?= $page->text()->blocks() ?>
    </div>
  </div>
</div>

<?php snippet('footer') ?>
