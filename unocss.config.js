import { defineConfig } from "unocss";
import { presetWind } from "@unocss/preset-wind";
import { presetDue } from "duecss";

export default defineConfig({
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
    },
    borderRadius: {
      DEFAULT: "0",
    },
  },
  shortcuts: [
    [
      /^content(?:-(fluid))?$/,
      ([, fluid], { theme }) =>
        `relative ${
          fluid ? "mx-lg" : `mx-auto w-[calc(100%-2*${theme.spacing.lg})]`
        }`,
    ],
    [
      /^content-(sm|md|lg|xl|2xl)(?:-(full|fluid))?$/,
      ([, bp, m], { theme }) => {
        let result = `relative max-w-screen-${bp}`;

        if (m === "fluid") result += " mx-lg";
        else result += ` mx-auto w-[calc(100%-2*${theme.spacing.lg})]`;

        if (m === "full") result += " w-full";

        return result;
      },
    ],
  ],
  presets: [presetWind(), presetDue()],
});
