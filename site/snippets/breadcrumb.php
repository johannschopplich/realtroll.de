<nav class="mb-5xl">
  <ul class="flex list-none flex-wrap justify-center text-center">
    <li class="after:content-[quoted:/] after:text-theme-text after:px-sm"><a href="<?= $site->homePage()->url() ?>" class="text-primary-400">Startseite</a></li>
    <li><a href="<?= $page->url() ?>" class="text-current"><?= $page->title()->escape() ?></a></li>
  </ul>
</nav>
