<nav class="due-mb-xl">
  <ul class="list-none flex flex-wrap justify-center text-center">
    <li class="after:content-[/] after:text-theme-text after:due-px-s"><a href="<?= $site->homePage()->url() ?>" class="text-primary-400">Startseite</a></li>
    <li><a href="<?= $page->url() ?>" class="text-current"><?= $page->title()->escape() ?></a></li>
  </ul>
</nav>
