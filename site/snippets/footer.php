  </main>

  <footer class="footer section text-center">
    <img class="pixelated mb-m" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" alt="Avatar von real Troll">
    <nav class="h-stacked d-flex justify-content-center">
      <a href="https://realtroll.hpage.com/">Blog</a>
      <a href="<?= url('impressum') ?>">Impressum</a>
      <a href="<?= url('datenschutzerklaerung') ?>">Datenschutzerklärung</a>
    </nav>
  </footer>

<?= js(['@template'], ['type' => 'module']) ?>

</body>
</html>
