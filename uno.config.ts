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
        DEFAULT: "#884A1F",
        50: "#E1A67E",
        100: "#DE9B6D",
        200: "#D6844C",
        300: "#CB6E2E",
        400: "#AA5C27",
        500: "#884A1F",
        600: "#673818",
        700: "#462610",
        800: "#180D06",
        900: "#000000",
      },
      link: {
        DEFAULT: "var(--un-color-link)",
        hover: "var(--un-color-link-hover)",
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
    ["expanded", { "font-stretch": "expanded" }],
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
      /^button-(.+?)(?:-?(outlined))?$/,
      ([, color, outlined]) => {
        const buttonBase = [
          "inline-flex items-center justify-center",
          "select-none rounded px-3 py-2 leading-none",
          "disabled:opacity-75 disabled:cursor-not-allowed",
          "aria-disabled:opacity-75 aria-disabled:cursor-not-allowed",
        ].join(" ");

        if (color === "text") {
          return `${buttonBase} bg-transparent text-theme-base hover:underline hover:decoration-[length:var(--un-decoration-thickness)] focus-visible:underline focus-visible:decoration-[length:var(--un-decoration-thickness)]`;
        }

        if (outlined) {
          return `${buttonBase} ring ring-inset ring-${color} bg-transparent text-${color} hover:bg-${color} hover:text-white focus-visible:bg-${color} focus-visible:text-white`;
        }

        return `${buttonBase} bg-${color} text-white hover:bg-${color}-600 focus-visible:bg-${color}-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-${color}`;
      },
    ],
  ],
  safelist: ["sr-only", "invisible"],
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
