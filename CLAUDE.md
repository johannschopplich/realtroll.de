# realtroll.de

Website for the RPG Maker game developer real Troll: Kirby 5 (PHP),
UnoCSS preset-wind4, Vite, TypeScript. Package manager: pnpm.

- UnoCSS class order: shortcuts/marker utilities (`pixelated`, `content`, …) →
  `[--var:…]` assignments → position/layout → display/flex/grid → spacing →
  sizing → typography → colors/background → border → effects/transitions →
  `after:`/`before:` block → state variants → breakpoint block last. Everything
  with a breakpoint prefix (`md:p-5xl`, `md:group-hover:z-2`) gathers at the
  very end of the string: `max-*` first, then ascending `sm:` → `md:` → `lg:` →
  `xl:`; within one breakpoint loosely follow the base group order, with
  breakpoint+state combos at the end of that run. Loose ordering within groups.
- The UnoCSS scanner only sees `site/{snippets,templates}/**/*` – classes that
  exist only in TS need inline styles or a safelist.
