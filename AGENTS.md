# realtroll.de

Website of the RPG Maker game developer real Troll.

## Commands

```bash
pnpm dev                           # frontend only; PHP on Herd or `composer start`
pnpm build
pnpm lint
pnpm format:check
pnpm test:types
scripts/sync-content.sh --upload   # dry run; --apply rsyncs storage/content (not in git), --download pulls
```

## Conventions

- UnoCSS class order: shortcuts/marker utilities (`pixelated`, `content`, …) →
  `[--var:…]` assignments → position/layout → display/flex/grid → spacing →
  sizing → typography → colors/background → border → effects/transitions →
  `after:`/`before:` block → state variants → breakpoint block last. Everything
  with a breakpoint prefix (`md:p-5xl`, `md:group-hover:z-2`) gathers at the
  very end of the string: `max-*` first, then ascending `sm:` → `md:` → `lg:` →
  `xl:`; within one breakpoint loosely follow the base group order, with
  breakpoint+state combos at the end of that run. Loose ordering within groups.
- Vertical section-gaps by role: navbar → first content (headerless pages)
  `5xl`; title/header → body `7xl` (hero pages like `game` keep `8xl`);
  trailing nav (section-divider, back-link, pagination) `8xl`.
