<?php snippet('header') ?>

<div class="container is-lg mb-xxl">
  <h1 class="editorial-title"><?= $page->text()->kti() ?></h1>
</div>

<div class="container is-xl is-fullwidth">
  <?php snippet('games') ?>
</div>

<?php snippet('footer') ?>
