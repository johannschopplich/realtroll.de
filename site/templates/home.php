<?php snippet('layout', slots: true) ?>

<div class="content-lg mb-7xl">
  <h1 class="editorial-title"><?= $page->text()->kti() ?></h1>
</div>

<div class="content-xl-full">
  <?php snippet('games') ?>
</div>

<?php endsnippet() ?>
