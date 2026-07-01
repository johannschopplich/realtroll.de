import type { Theme } from "@unocss/preset-wind4";
import presetIcons from "@unocss/preset-icons";
import { defineConfig, presetWind4 } from "unocss";

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
        DEFAULT: "var(--un-color-primary)",
        50: "var(--un-color-primary-50)",
        100: "var(--un-color-primary-100)",
        200: "var(--un-color-primary-200)",
        300: "var(--un-color-primary-300)",
        400: "var(--un-color-primary-400)",
        500: "var(--un-color-primary-500)",
        600: "var(--un-color-primary-600)",
        700: "var(--un-color-primary-700)",
        800: "var(--un-color-primary-800)",
        900: "var(--un-color-primary-900)",
        950: "var(--un-color-primary-950)",
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
    default: {
      transition: {
        duration: "200ms",
      },
    },
    shadow: {
      solid: "4px 4px 0 var(--un-color-primary-700)",
      float: [
        "0 16px 26px oklch(10% 0.035 52 / 0.42)",
        "0 5px 10px oklch(10% 0.035 52 / 0.32)",
      ],
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
    animation: {
      keyframes: {
        "devlog-blink": "{50%{opacity:0}}",
      },
      durations: {
        "devlog-blink": "1.125s",
      },
      timingFns: {
        "devlog-blink": "steps(1)",
      },
      counts: {
        "devlog-blink": "infinite",
      },
    },
  },
  rules: [
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
    [
      "crt-overlay",
      {
        "pointer-events": "none",
        background: [
          "radial-gradient(75% 55% at 50% 34%, rgb(255 255 255 / 0.12), transparent 60%)",
          "radial-gradient(130% 130% at 50% 50%, transparent 42%, rgb(0 0 0 / 0.6) 100%)",
          "repeating-linear-gradient(rgb(0 0 0 / 0.26) 0 1px, transparent 1px 3px)",
        ].join(", "),
        "box-shadow": "inset 0 0 4rem rgb(0 0 0 / 0.7)",
      },
    ],
    [
      "recess-overlay",
      {
        "box-shadow":
          "inset 0 8px 14px -7px rgb(0 0 0 / 0.65), inset 0 0 0 1px rgb(0 0 0 / 0.15)",
      },
    ],
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
      /^content-([a-z0-9]+)$/,
      ([, bp], { theme }) => {
        const maxWidth = bp === "prose" ? `max-w-${bp}` : `max-w-screen-${bp}`;
        return `relative mx-auto ${maxWidth} w-[calc(100%-2*${(theme as Theme)?.spacing?.lg})]`;
      },
    ],
    [
      /^button-(primary|text)(?:-?(outlined))?$/,
      ([, color, outlined]) => {
        const buttonBase = [
          "inline-flex items-center justify-center",
          "px-3 py-2 leading-none rounded select-none",
          "disabled:opacity-75 disabled:cursor-not-allowed",
          "aria-disabled:opacity-75 aria-disabled:cursor-not-allowed",
        ].join(" ");

        if (color === "text") {
          return `${buttonBase} text-theme-base bg-transparent active:scale-97 hover:underline hover:decoration-[length:var(--un-decoration-thickness)] focus-visible:underline focus-visible:decoration-[length:var(--un-decoration-thickness)]`;
        }

        const sharedStyles = [
          `border-2 border-${color}-700 shadow-solid`,
          "active:translate-[4px] active:shadow-none",
          `focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-${color}`,
        ].join(" ");

        if (outlined) {
          return `${buttonBase} ${sharedStyles} text-${color} bg-theme-background ring ring-inset ring-${color}`;
        }

        return `${buttonBase} ${sharedStyles} text-white bg-${color}`;
      },
    ],
    [
      /^chip-row-(sm|base|lg)$/,
      ([, size]) =>
        `flex flex-wrap items-center font-medium ${
          size === "lg"
            ? "gap-2.5 text-sm"
            : size === "sm"
              ? "gap-1.5 text-xs"
              : "gap-2 text-xs"
        }`,
    ],
    [
      /^chip-(bevel|glass)-(sm|base|lg)$/,
      ([, appearance, size]) => {
        const surface =
          appearance === "bevel"
            ? "text-primary-800 bg-primary-100 border-t-primary-50 border-l-primary-50 border-b-primary-400 border-r-primary-400"
            : "text-primary-50 bg-white/10 backdrop-blur-[1px] border-t-white/30 border-l-white/30 border-b-primary-950/60 border-r-primary-950/60";
        return `${
          size === "lg"
            ? "px-2.5 py-1.5"
            : size === "sm"
              ? "px-1.5 py-0.5 text-xs"
              : "px-2 py-1"
        } leading-none ${surface} border-2`;
      },
    ],
    {
      "corner-square":
        "absolute block bg-theme-background border-2 border-primary-700 pointer-events-none",
      "display-title":
        "font-heading font-700 leading-none text-[clamp(var(--un-text-3xl),5vw,4rem)] text-primary-700",
      "label-caps": "font-medium uppercase tracking-wider",
    },
  ],
  variants: [
    (matcher) => {
      if (!matcher.startsWith("hidpi:")) return matcher;
      return {
        matcher: matcher.slice("hidpi:".length),
        handle: (input, next) =>
          next({
            ...input,
            parent: `${input.parent ? `${input.parent} $$ ` : ""}@media (min-resolution: 2x), (-webkit-min-device-pixel-ratio: 2)`,
          }),
      };
    },
  ],
  safelist: [
    "sr-only",
    "invisible",
    // Enforce `--spacing-9xl` to be generated
    ...["xl", "9xl"].flatMap((size) => [`pt-${size}`]),
    ...["sm", "base", "lg"].flatMap((size) => [
      `chip-row-${size}`,
      `chip-bevel-${size}`,
      `chip-glass-${size}`,
    ]),
  ],
  outputToCssLayers: true,
  presets: [
    presetWind4({
      preflights: {
        reset: true,
      },
    }),
    presetIcons({
      extraProperties: {
        display: "inline-block",
        width: "1.1em",
        height: "1.1em",
        "vertical-align": "text-bottom",
      },
    }),
  ],
});
