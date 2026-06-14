<?php

/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Page $page */

$blog = page('blog');
$games = page('spiele');
$navLink = 'link-default underline-offset-4 text-primary-700';

$onGameTree = $games?->isAncestorOf($page);
$crumbs = [];
if ($onGameTree) {
  foreach ($page->parents()->flip() as $ancestor) {
    if ($ancestor->is($games)) continue;
    $crumbs[] = ['label' => $ancestor->title()->value(), 'url' => $ancestor->url()];
  }
  $crumbs[] = ['label' => $page->title()->value(), 'url' => null];
}

$subpages = $onGameTree ? $page->children()->listed() : null;

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
  class="group sticky top-0 z-10 flex items-center justify-between px-lg h-14 border-b-2 border-transparent transition-colors duration-200 data-[scrolled=true]:bg-theme-background data-[scrolled=true]:border-primary-700 md:px-5xl"
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
          <span class="shrink-0 text-primary-700/40" aria-hidden="true">&rsaquo;</span>
          <?php if ($crumb['url']): ?>
            <a href="<?= $crumb['url'] ?>" class="link-default shrink-0 underline-offset-4 hover:text-primary-700"><?= esc($crumb['label']) ?></a>
          <?php elseif ($subpages?->isNotEmpty()): ?>
            <details class="relative shrink-0" data-subpage-menu>
              <summary class="flex items-center gap-1 min-h-6 cursor-pointer list-none text-primary-700 [&::-webkit-details-marker]:hidden">
                <?= esc($crumb['label']) ?>
                <svg class="size-3 motion-safe:transition-transform motion-safe:duration-200 [details[open]_&]:rotate-180" viewBox="0 0 12 12" fill="none" focusable="false" aria-hidden="true">
                  <path d="M2.5 4.5 6 8l3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="square"></path>
                </svg>
              </summary>
              <ul
                data-subpage-panel
                class="absolute left-0 top-full z-20 flex flex-col min-w-[9rem] p-1 list-none bg-theme-background border-2 border-primary-700 shadow-[4px_4px_0_var(--un-color-primary-700)]"
              >
                <?php foreach ($subpages as $subpage): ?>
                  <li>
                    <a href="<?= $subpage->url() ?>" class="link-default block px-2 py-1 text-sm font-medium underline-offset-4 text-primary-700/80 hover:text-primary-700"><?= esc($subpage->title()) ?></a>
                  </li>
                <?php endforeach ?>
              </ul>
            </details>
          <?php else: ?>
            <span class="truncate text-primary-700" aria-current="page"><?= esc($crumb['label']) ?></span>
          <?php endif ?>
        <?php endforeach ?>
      </nav>
    <?php endif ?>
  </div>
  <div class="flex items-center gap-lg text-sm font-medium tracking-tight">
    <?php foreach ($navItems as $item): ?>
      <a <?= attr([
        'href' => $item['url'],
        'class' => $navLink,
        'aria-current' => $item['current'] ? 'page' : null,
      ]) ?>>
        <?= esc($item['label']) ?>
      </a>
    <?php endforeach ?>
  </div>
</nav>
