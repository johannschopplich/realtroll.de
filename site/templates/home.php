<?php snippet('layout', slots: true) ?>

<div class="content-xl mb-7xl md:px-5xl">
  <h1 class="editorial-title"><?= $page->text()->kti() ?></h1>
</div>

<div class="content-xl">
  <?php snippet('games') ?>
</div>

<?php endsnippet() ?>
