<div align="center">
  <img src="./public/assets/img/icons/favicon.svg" alt="real Troll logo" width="120">

# realtroll.de

Home for real Troll's RPG Maker games.

[Website](https://realtroll.de) •
[Blog](https://realtroll.de/blog)

</div>

## Why

real Troll has been building RPG Maker games for nearly two decades – Endzeit, Wolfenhain, El Dorado, and now Nachbarlicht. The old home, a [hpage](https://realtroll.hpage.com) frameset, carried a download for every game and a devlog reaching back to 2007, but the platform had run its course.

This rebuild replaces it. Every game is playable right in the browser, the 447-post devlog (January 2007 – June 2026) has moved into Kirby, and the design sits between two worlds: the pixel art of the games and a quiet site around it.

## How It's Built

- [Kirby 5](https://getkirby.com) – flat-file CMS, content as folders and text files
- [UnoCSS](https://unocss.dev) (preset-wind4) + [Vite](https://vite.dev) + TypeScript – styling and frontend build
- [EasyRPG Player](https://easyrpg.org) (WebAssembly) – runs the games in the browser
- [kirby-helpers](https://github.com/johannschopplich/kirby-helpers) – metadata, redirects, and env helpers

## Playing in the Browser

Each game's files live under `public/play/games/<folder>`; the EasyRPG Player web port (WebAssembly) boots a game via `/play/?game=<folder>`. Games without bundled files fall back to a download link.

The web build needs two things the desktop player doesn't, both provided on demand by the `easyrpg` plugin (`site/plugins/easyrpg`):

- **A file index.** The route `play/games/<folder>/index.json` generates the file map the player requires – a PHP port of EasyRPG's [`gencache`](https://github.com/EasyRPG/Tools/tree/master/gencache) tool – and caches it.
- **An ExFont bitmap.** It extracts the per-game ExFont embedded in `RPG_RT.exe` into `ExFont.bmp`, because the Emscripten build can't read it from the executable like the desktop build does – otherwise custom glyphs silently vanish.

### Building the Player

`public/play/index.js` and `public/play/index.wasm` are built in Docker from [`scripts/player/Dockerfile`](scripts/player/Dockerfile), with no local toolchain:

```bash
pnpm build:player
```

This compiles the libraries from EasyRPG's [buildscripts](https://github.com/EasyRPG/buildscripts) and the Player at the commit pinned as `PLAYER_REF` in the Dockerfile – for now a fork commit with the save manager, until [EasyRPG/Player#3643](https://github.com/EasyRPG/Player/pull/3643) merges. Later runs reuse the cached library layer. To update the Player, bump `PLAYER_REF` to a full commit hash, and `LIBLCF_REF` too when the Player needs a newer liblcf; a branch name would let Docker reuse a stale cached build.

Afterwards, bump the `?v=` on the `index.js` script tag in `public/play/index.html`, so browsers fetch the new `index.js` and `index.wasm` together.

## Development

1. Create your `.env` from the example:

   ```bash
   cp .env.development.example .env
   ```

2. Install dependencies:

   ```bash
   pnpm install
   composer install
   ```

[`AGENTS.md`](./AGENTS.md) lists the commands. Pushes to `main` deploy through [`.github/workflows/deploy.yml`](./.github/workflows/deploy.yml).

## Content & Copyright

This repository holds the site's code; the games, artwork, and writing it renders are real Troll's own and are not part of this license.

## License

[MIT](./LICENSE) License © 2021-PRESENT [Johann Schopplich](https://github.com/johannschopplich)
