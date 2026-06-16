<?php

/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Page $page */

$blog = page('blog');
$games = page('spiele');

$onGameTree = $games?->isAncestorOf($page);
$crumbs = [];
if ($onGameTree) {
  foreach ($page->parents()->flip() as $ancestor) {
    if ($ancestor->is($games)) continue;
    $crumbs[] = $ancestor;
  }
  $crumbs[] = $page;
}

$sections = $onGameTree ? $crumbs[0]->children()->listed() : null;

$navItems = [
  ['label' => 'Spiele', 'url' => $site->homePage()->url(), 'current' => $page->isHomePage() || $games?->isOpen()],
  ...($blog ? [
    ['label' => 'Blog', 'url' => $blog->url(), 'current' => $blog->isOpen()],
  ] : []),
];

?>
<nav
  id="main-nav"
  data-scrolled="false"
  class="group sticky top-0 z-10 flex items-center justify-between gap-xl px-lg h-14 border-b-2 border-transparent transition-colors duration-200 data-[scrolled=true]:bg-theme-background data-[scrolled=true]:border-primary-700 md:px-5xl"
  aria-label="Hauptnavigation"
>
  <span class="corner-square -bottom-px left-0 size-2 -translate-x-1/2 translate-y-1/2 opacity-0 transition-opacity duration-200 group-data-[scrolled=true]:opacity-100" aria-hidden="true"></span>
  <span class="corner-square -bottom-px right-0 size-2 translate-x-1/2 translate-y-1/2 opacity-0 transition-opacity duration-200 group-data-[scrolled=true]:opacity-100" aria-hidden="true"></span>
  <div class="flex items-center gap-2 min-w-0">
    <a
      href="<?= $site->homePage()->url() ?>"
      class="flex items-center gap-2 shrink-0 text-sm font-medium tracking-tight text-primary-700"
      <?php e($page->isHomePage(), 'aria-current="page"') ?>
      aria-label="Startseite"
    >
      <img class="pixelated" src="/assets/img/icons/favicon-32x32.png" width="24" height="24" alt="">
      <span class="<?php e($onGameTree, 'hidden sm:inline') ?>">real Troll</span>
    </a>
    <?php if ($onGameTree): ?>
      <nav class="flex items-center gap-2 min-w-0 text-sm font-medium tracking-tight text-primary-700/70" aria-label="Brotkrumen">
        <?php foreach ($crumbs as $crumb): ?>
          <span class="shrink-0 text-primary-700/40" aria-hidden="true">/</span>
          <?php if (!$crumb->is($page)): ?>
            <a href="<?= $crumb->url() ?>" class="link-default shrink-0 underline-offset-4 hover:text-primary-700"><?= esc($crumb->title()) ?></a>
          <?php elseif ($sections?->isNotEmpty()): ?>
            <details class="relative shrink-0" data-subpage-menu>
              <summary class="flex items-center gap-1 min-h-6 cursor-pointer list-none text-primary-700 [&::-webkit-details-marker]:hidden">
                <?= esc($crumb->title()) ?>
                <span class="inline-block text-[1.2em] leading-none motion-safe:transition-transform motion-safe:duration-200 [details[open]_&]:rotate-180" aria-hidden="true">&#x25BE;</span>
              </summary>
              <ul
                data-subpage-panel
                class="absolute left-0 top-full z-20 flex flex-col gap-1 p-1 min-w-[9rem] list-none bg-theme-background border-2 border-primary-700 shadow-solid"
              >
                <?php foreach ($sections as $section): ?>
                  <li>
                    <a
                      href="<?= $section->url() ?>"
                      class="link-default [--un-decoration-offset:2px] block px-2 py-0.5 text-sm font-medium leading-tight text-primary-700/80 hover:text-primary-700"
                      <?php e($section->is($page), 'aria-current="page"') ?>
                    ><?= esc($section->title()) ?></a>
                  </li>
                <?php endforeach ?>
              </ul>
            </details>
          <?php else: ?>
            <span class="truncate text-primary-700" aria-current="page"><?= esc($crumb->title()) ?></span>
          <?php endif ?>
        <?php endforeach ?>
      </nav>
    <?php endif ?>
  </div>
  <div class="flex items-center gap-lg text-sm font-medium tracking-tight">
    <?php foreach ($navItems as $item): ?>
      <a <?= attr([
        'href' => $item['url'],
        'class' => 'link-default underline-offset-4 text-primary-700',
        'aria-current' => $item['current'] ? 'page' : null,
      ]) ?>>
        <?= esc($item['label']) ?>
      </a>
    <?php endforeach ?>
  </div>
</nav>
