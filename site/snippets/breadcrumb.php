<nav class="breadcrumb mb-xl">
  <ul class="justify-content-center text-center">
    <li><a href="<?= $site->homePage()->url() ?>">Startseite</a></li>
    <li><a href="<?= $page->url() ?>"><?= $page->title()->html() ?></a></li>
  </ul>
</nav>
