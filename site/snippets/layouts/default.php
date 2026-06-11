<?php

/** @var \Kirby\Cms\App $kirby */
/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Page $page */
/** @var mixed $slot */

?>
<!doctype html>
<html class="var-color-primary var-color-primary-100 var-color-primary-200 var-color-primary-300 var-color-primary-700" lang="de">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= $page->customTitle()->or($page->title() . ' – ' . $site->title()) ?></title>

  <?php $meta = $page->meta() ?>
  <?= $meta->robots() ?>
  <?= $meta->jsonld() ?>
  <?= $meta->social() ?>

  <link rel="icon" href="/assets/img/icons/favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="/assets/img/icons/favicon-16x16.png" sizes="16x16" type="image/png">

  <?php if (vite()->isDev()): ?>
    <?= css('assets/dev/index.css?v=' . time(), ['id' => 'vite-dev-css']) ?>
  <?php endif ?>

  <?= vite()->js('main.ts') ?>
  <?= vite()->css('main.ts') ?>

  <?= css([
    'assets/fonts/PPMondwest.css',
    'assets/fonts/IosevkaSlab.css'
  ]) ?>

  <script async defer data-domain="realtroll.de" src="https://plausible.io/js/plausible.js"></script>

</head>
<body class="min-h-dvh overflow-x-clip" data-template="<?= $page->intendedTemplate()->name() ?>">

  <div id="floating-screenshot" class="fixed top-[0] left-[0] z-10 pointer-events-none hidden w-[320px] aspect-[4/3] translate-x-[var(--mouseX)] translate-y-[var(--mouseY)] items-center justify-center children:hidden">
    <img class="pixelated scale-[2] origin-top-left">
  </div>

  <?php
  $blog = page('blog');
  $games = page('spiele');
  $onGames = $page->isHomePage() || ($games && ($page->is($games) || $page->parents()->has($games)));
  $onBlog = $blog && ($page->is($blog) || $page->parents()->has($blog));
  $navLink = 'text-primary-700 decoration-[length:var(--un-decoration-thickness)] underline-offset-4 hover:underline aria-[current]:underline';
  ?>

  <div class="relative z-1 min-h-dvh bg-polka-grid border-b-2 border-primary-700">
    <nav
      id="main-nav"
      data-scrolled="false"
      class="group sticky top-0 z-40 h-14 flex items-center justify-between px-lg md:px-5xl border-b-2 border-transparent transition-colors duration-200 data-[scrolled=true]:bg-theme-background data-[scrolled=true]:border-primary-700"
      aria-label="Hauptnavigation"
    >
      <span class="corner-square -bottom-px left-0 size-2 -translate-x-1/2 translate-y-1/2 opacity-0 transition-opacity duration-200 group-data-[scrolled=true]:opacity-100" aria-hidden="true"></span>
      <span class="corner-square -bottom-px right-0 size-2 translate-x-1/2 translate-y-1/2 opacity-0 transition-opacity duration-200 group-data-[scrolled=true]:opacity-100" aria-hidden="true"></span>
      <a
        href="<?= $site->homePage()->url() ?>"
        class="flex items-center gap-2 font-heading text-lg text-primary-700 leading-none"
        <?php e($page->isHomePage(), 'aria-current="page"') ?>
        aria-label="Startseite"
      >
        <img class="pixelated" src="/assets/img/icons/favicon-32x32.png" width="24" height="24" alt="">
        real Troll
      </a>
      <div class="flex items-center gap-lg text-sm tracking-wide">
        <a <?= attr([
          'href' => $site->homePage()->url(),
          'class' => $navLink,
          'aria-current' => $onGames ? 'page' : null,
        ]) ?>>
          Spiele
        </a>
        <?php if ($blog): ?>
          <a <?= attr([
            'href' => $blog->url(),
            'class' => $navLink,
            'aria-current' => $onBlog ? 'page' : null,
          ]) ?>>
            Blog
          </a>
        <?php endif ?>
      </div>
    </nav>

    <?php if ($page->isHomePage()): ?>
      <header
        class="relative flex items-center justify-center pixelated border-b-2 border-primary-700 pt-6xl pb-8xl md:pt-7xl md:pb-9xl"
        style="background: url('<?= asset('assets/img/bg-pattern.svg')->url() ?>') center repeat fixed"
        aria-hidden="true"
      >
        <img class="pixelated md:scale-[1.5]" src="<?= asset('assets/img/willkommen.gif')->url() ?>" alt="Willkommen auf realtroll.de">
        <img
          class="pixelated absolute bottom-0 left-1/2 -translate-x-1/2 z-10 max-w-none origin-bottom scale-[1.5] md:scale-[2]"
          src="<?= asset('assets/img/editorial-gesichter.png')->url() ?>"
          width="207"
          height="42"
          alt=""
        >
      </header>
    <?php endif ?>

    <main id="main" class="<?= $page->isHomePage() ? 'mt-5xl md:mt-7xl' : 'mt-5xl' ?>">
      <?= $slot ?>
    </main>

    <div class="pt-9xl"></div>
    <?php snippet('components/corner-squares', ['corners' => ['bottom-left', 'bottom-right'], 'size' => 2]) ?>
  </div>

  <footer class="sticky bottom-0 z-0 bg-theme-background">
    <div class="relative py-7xl text-center bg-graph-paper">
      <div class="absolute inset-0 overflow-hidden pointer-events-none select-none" aria-hidden="true">
        <p class="absolute inset-x-0 bottom-0 translate-y-1/5 font-heading text-[18vw] leading-none text-primary-700 opacity-5 whitespace-nowrap text-center">real Troll</p>
      </div>
      <img class="pixelated mx-auto mb-lg" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" alt="Avatar von real Troll">
      <nav class="flex gap-lg justify-center flex-wrap mb-lg text-sm">
        <?php if ($blog): ?>
          <a href="<?= $blog->url() ?>">Blog</a>
        <?php endif ?>
        <a href="<?= url('impressum') ?>">Impressum</a>
        <a href="<?= url('datenschutzerklaerung') ?>">Datenschutzerklärung</a>
      </nav>
      <p class="text-sm text-contrast-medium">© <?= date('Y') ?> real Troll</p>
    </div>
  </footer>

</body>
</html>
