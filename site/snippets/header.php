<!doctype html>
<html lang="de">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?= $page->metaTags() ?>

  <?= css([
    'https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap',
    'assets/css/buldy.min.css',
    'assets/css/index.css'
  ]) ?>

</head>
<body>

  <img id="custom-cursor" hidden>

  <header class="editorial centered-content mb-xxl<?= r($page->isHomePage(), ' is-homepage') ?>">
    <?php if ($page->isHomePage()): ?>
      <img class="editorial-image pixelated" src="<?= asset('assets/img/willkommen.gif')->url() ?>" alt="Willkommensgruß">
    <?php endif ?>
  </header>

  <main id="main">
