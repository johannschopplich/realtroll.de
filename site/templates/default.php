<?php snippet('header') ?>

<div class="container is-lg mb-xxl">
  <h1 class="editorial-title"><?= $page->title()->html() ?></h1>
</div>

<div class="container for-content">
  <div class="content">
    <?= $page->text()->blocks() ?>
  </div>
</div>

<?php snippet('footer') ?>
