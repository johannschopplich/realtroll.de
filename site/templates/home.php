<?php

/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true);

?>

<h1 class="sr-only"><?= $site->title()->escape() ?></h1>

<div class="content-xl">
  <?php snippet('components/games') ?>
</div>

<?php endsnippet() ?>
