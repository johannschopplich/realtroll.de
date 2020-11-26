  </main>

  <footer class="footer section text-center">
    <img class="pixelated mb-m" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" alt="Avatar von real Troll">
    <nav class="columns is-centered has-gap-m">
      <a class="column is-narrow" href="https://realtroll.hpage.com/">Blog von real Troll</a>
      <a class="column is-narrow" href="<?= url('impressum') ?>">Impressum</a>
    </nav>
  </footer>

<?= js(['@auto'], ['type' => 'module']) ?>

</body>
</html>
