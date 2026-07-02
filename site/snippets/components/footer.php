<?php

$socials = [
  'instagram' => ['icon' => 'i-dinkie-icons-instagram', 'label' => 'Instagram'],
  'youtube' => ['icon' => 'i-dinkie-icons-television-filled', 'label' => 'YouTube'],
  'rss' => ['icon' => 'i-dinkie-icons-wireless', 'label' => 'RSS-Feed', 'url' => url('feeds/rss')]
];

$socialLinks = site()->social()->toStructure()->filter(function ($entry) use ($socials) {
  $social = $socials[$entry->platform()->value()] ?? null;
  return $social && (isset($social['url']) || $entry->url()->isNotEmpty());
});

$columns = site()->footerColumns()->toStructure()->filter(
  fn ($column) => $column->heading()->isNotEmpty() && $column->links()->toPages()->isNotEmpty()
);

$columnCount = 1 + $columns->count() + ($socialLinks->isNotEmpty() ? 1 : 0);

?>
<footer class="sticky bottom-0 z-0 bg-theme-background">
  <div class="bg-graph-paper relative pt-7xl pb-3xl md:pt-[calc(var(--spacing-9xl)+var(--spacing-xl))]">
    <div class="absolute inset-0 overflow-hidden pointer-events-none select-none" aria-hidden="true">
      <svg class="absolute inset-x-0 bottom-0 mx-auto w-full max-w-screen-lg opacity-5" viewBox="40 0 3880 760" focusable="false">
        <text x="0" y="760" font-size="1000" text-anchor="start" class="font-heading font-bold fill-primary-700">real Troll</text>
      </svg>
    </div>

    <div class="content-lg">
      <div
        class="
          grid gap-x-7xl gap-y-4xl
          text-center
          md:grid-cols-[repeat(var(--footer-cols),auto)] md:justify-center md:items-start md:text-left
        "
        style="--footer-cols: <?= $columnCount ?>"
      >
        <div class="relative flex justify-center md:block md:w-12 md:self-stretch">
          <img class="pixelated md:absolute md:bottom-[0.25em] md:left-0" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" width="48" height="96" alt="Avatar von real Troll">
        </div>

        <?php foreach ($columns as $column): ?>
          <nav class="flex flex-col items-center md:items-start" aria-label="<?= $column->heading()->escape() ?>">
            <p class="label-caps mb-2 text-xs text-contrast-medium"><?= $column->heading()->escape() ?></p>
            <?php foreach ($column->links()->toPages() as $linkPage): ?>
              <a href="<?= $linkPage->url() ?>" class="link-default text-sm"><?= $linkPage->title()->escape() ?></a>
            <?php endforeach ?>
          </nav>
        <?php endforeach ?>

        <?php if ($socialLinks->isNotEmpty()): ?>
          <nav class="flex flex-col items-center md:items-start" aria-label="Folgen">
            <p class="label-caps mb-2 text-xs text-contrast-medium">Folgen</p>
            <ul class="group flex items-center gap-3" role="list">
              <?php foreach ($socialLinks as $link): ?>
                <?php $social = $socials[$link->platform()->value()] ?>
                <li>
                  <a
                    href="<?= $social['url'] ?? $link->url()->escape() ?>"
                    class="
                      inline-flex
                      text-xl
                      transition-opacity
                      group-hover:not-hover:opacity-60 group-has-[:focus-visible]:not-focus-visible:opacity-60
                    "
                    target="_blank"
                    rel="noopener"
                    aria-label="<?= $social['label'] ?>"
                  >
                    <span class="<?= $social['icon'] ?>" aria-hidden="true"></span>
                  </a>
                </li>
              <?php endforeach ?>
            </ul>
          </nav>
        <?php endif ?>
      </div>
    </div>
  </div>
</footer>
