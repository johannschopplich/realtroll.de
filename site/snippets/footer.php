  </main>

  <footer class="due-py-xl text-center">
    <img class="pixelated mx-auto due-mb-m" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" alt="Avatar von real Troll">
    <nav class="flex justify-center space-x-5">
      <a href="https://realtroll.hpage.com/">Blog</a>
      <a href="<?= url('impressum') ?>">Impressum</a>
      <a href="<?= url('datenschutzerklaerung') ?>">Datenschutzerklärung</a>
    </nav>
  </footer>

<?= jsTpl(['type' => 'module']) ?>

</body>
</html>
