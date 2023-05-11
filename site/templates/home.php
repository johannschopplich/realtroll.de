<?php snippet('layout', slots: true) ?>

<div class="content-xl mb-5xl md:px-5xl md:mb-7xl">
  <h1 class="editorial-title"><?= $page->text()->kti() ?></h1>
</div>

<div class="content-xl">
  <?php snippet('games') ?>
</div>

<?php endsnippet() ?>
