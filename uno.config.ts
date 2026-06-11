import type { Theme } from "@unocss/preset-wind4";
import { parseColor } from "@unocss/preset-wind4/utils";
import { defineConfig, presetWind4, transformerDirectives } from "unocss";

export default defineConfig<Theme>({
  cli: {
    entry: {
      patterns: ["site/{snippets,templates}/**/*"],
      outFile: "src/styles/uno.css",
    },
  },
  theme: {
    colors: {
      primary: {
        DEFAULT: "oklch(52% 0.105 52)",
        50: "oklch(97.5% 0.012 52)",
        100: "oklch(94% 0.025 52)",
        200: "oklch(88% 0.05 52)",
        300: "oklch(78% 0.085 52)",
        400: "oklch(65% 0.105 52)",
        500: "oklch(52% 0.105 52)",
        600: "oklch(43% 0.095 52)",
        700: "oklch(36% 0.08 52)",
        800: "oklch(29% 0.065 52)",
        900: "oklch(24% 0.05 52)",
        950: "oklch(18% 0.035 52)",
      },
      theme: {
        base: "var(--un-color-text)",
        background: "var(--un-color-background)",
      },
      contrast: {
        lowest: "var(--un-color-contrast-lowest)",
        lower: "var(--un-color-contrast-lower)",
        low: "var(--un-color-contrast-low)",
        medium: "var(--un-color-contrast-medium)",
        high: "var(--un-color-contrast-high)",
        higher: "var(--un-color-contrast-higher)",
      },
    },
    radius: {
      DEFAULT: "0",
    },
    text: {
      xs: { fontSize: "0.75rem", lineHeight: "var(--un-line-height-normal)" },
      sm: { fontSize: "0.875rem", lineHeight: "var(--un-line-height-normal)" },
      base: { fontSize: "1rem", lineHeight: "var(--un-line-height-normal)" },
      lg: {
        fontSize: "var(--un-text-lg)",
        lineHeight: "var(--un-line-height-heading)",
      },
      xl: {
        fontSize: "var(--un-text-xl)",
        lineHeight: "var(--un-line-height-heading)",
      },
      "2xl": {
        fontSize: "var(--un-text-2xl)",
        lineHeight: "var(--un-line-height-heading)",
      },
      "3xl": {
        fontSize: "var(--un-text-3xl)",
        lineHeight: "var(--un-line-height-heading)",
      },
      "4xl": {
        fontSize: "var(--un-text-4xl)",
        lineHeight: "var(--un-line-height-heading)",
      },
    },
    font: {
      normal: "var(--un-font-family-normal)",
      heading: "var(--un-font-family-heading)",
    },
    leading: {
      normal: "var(--un-line-height-normal)",
      heading: "var(--un-line-height-heading)",
    },
    container: {
      prose: "75ch",
    },
  },
  rules: [
    [
      /^var-color-(.+)$/,
      ([, body], { theme }) => {
        const parsed = parseColor(body, theme);
        if (!parsed) return;

        return {
          [`--un-color-${body}`]: parsed.color,
        };
      },
    ],
    [
      "hyphenate",
      {
        "overflow-wrap": "break-word",
        "-webkit-hyphens": "auto",
        "-webkit-hyphenate-limit-before": "3",
        "-webkit-hyphenate-limit-after": "3",
        "-webkit-hyphenate-limit-chars": "8 4 4",
        "-webkit-hyphenate-limit-lines": "2",
        "-webkit-hyphenate-limit-last": "always",
        "-webkit-hyphenate-limit-zone": "8%",
        hyphens: "auto",
        "hyphenate-limit-chars": "8 4 4",
        "hyphenate-limit-lines": "2",
        "hyphenate-limit-last": "always",
        "hyphenate-limit-zone": "8%",
      },
    ],
    ["pixelated", { "image-rendering": "pixelated" }],
  ],
  shortcuts: [
    [
      /^content(?:-(fluid))?$/,
      ([, fluid], { theme }) =>
        `relative ${
          fluid ? "mx-lg" : `mx-auto w-[calc(100%-2*${theme?.spacing?.lg})]`
        }`,
    ],
    [
      /^content-(.+)$/,
      ([, bp], { theme }) => {
        const maxWidth = bp === "prose" ? `max-w-${bp}` : `max-w-screen-${bp}`;
        return `relative ${maxWidth} mx-auto w-[calc(100%-2*${(theme as Theme)?.spacing?.lg})]`;
      },
    ],
    [/^column-(\d+)$/, ([, d]) => `flex-none w-${d}/12`],
    {
      columns: "flex flex-wrap",
      column: "block flex-1",
      "column-narrow": "block flex-[0_0_auto] w-auto",
      "column-auto": "block flex-1 w-auto",
      "column-full": "block flex-none w-full",
    },
    [
      /^button-(primary|text)(?:-?(outlined))?$/,
      ([, color, outlined]) => {
        const buttonBase = [
          "inline-flex items-center justify-center",
          "select-none rounded px-3 py-2 leading-none",
          "disabled:opacity-75 disabled:cursor-not-allowed",
          "aria-disabled:opacity-75 aria-disabled:cursor-not-allowed",
        ].join(" ");

        if (color === "text") {
          return `${buttonBase} active:scale-97 bg-transparent text-theme-base hover:underline hover:decoration-[length:var(--un-decoration-thickness)] focus-visible:underline focus-visible:decoration-[length:var(--un-decoration-thickness)]`;
        }

        const elevatedStyles = [
          `border-2 border-${color}-700`,
          `shadow-[4px_4px_0_var(--un-color-${color}-700)]`,
          "active:translate-[4px] active:shadow-none",
          `focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-${color}`,
        ].join(" ");

        if (outlined) {
          return `${buttonBase} ${elevatedStyles} ring ring-inset ring-${color} bg-theme-background text-${color}`;
        }

        return `${buttonBase} ${elevatedStyles} bg-${color} text-white`;
      },
    ],
    [
      // list-none/ps-0 guards against list styles when rendered inside .prose
      /^game-chips-(base|lg)$/,
      ([, size]) =>
        `list-none ps-0 flex flex-wrap items-center font-medium ${
          size === "lg" ? "text-sm gap-2.5" : "text-xs gap-2"
        }`,
    ],
    [
      /^game-chip-(bevel|glass)-(base|lg)$/,
      ([, appearance, size]) => {
        const surface =
          appearance === "bevel"
            ? "text-primary-800 bg-primary-100 border-t-primary-50 border-l-primary-50 border-b-primary-400 border-r-primary-400"
            : "text-primary-50 bg-white/10 border-t-white/30 border-l-white/30 border-b-primary-950/60 border-r-primary-950/60";
        return `border-2 leading-none ${surface} ${
          size === "lg" ? "px-2.5 py-1.5" : "px-2 py-1"
        }`;
      },
    ],
    {
      "corner-square":
        "hidden md:block absolute bg-theme-background border-2 border-primary-700 pointer-events-none",
      "editorial-title":
        "text-primary-700 font-heading font-700 leading-none text-[clamp(var(--un-text-3xl),5vw,4rem)]",
    },
  ],
  safelist: [
    "sr-only",
    "invisible",
    ...["base", "lg"].flatMap((size) => [
      `game-chips-${size}`,
      `game-chip-bevel-${size}`,
      `game-chip-glass-${size}`,
    ]),
  ],
  outputToCssLayers: true,
  transformers: [transformerDirectives()],
  presets: [
    presetWind4({
      preflights: {
        reset: true,
      },
    }),
  ],
});
