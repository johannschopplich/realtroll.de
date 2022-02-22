  </main>

  <footer class="py-5xl text-center">
    <img class="pixelated mx-auto mb-lg" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" alt="Avatar von real Troll">
    <nav class="flex justify-center space-x-lg">
      <a href="https://realtroll.hpage.com/">Blog</a>
      <a href="<?= url('impressum') ?>">Impressum</a>
      <a href="<?= url('datenschutzerklaerung') ?>">Datenschutzerklärung</a>
    </nav>
  </footer>

<?= jsTpl(['type' => 'module']) ?>

</body>
</html>
