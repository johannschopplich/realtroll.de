import { defineConfig } from "unocss";
import { presetWind } from "@unocss/preset-wind";
import { presetDue } from "duecss";

export default defineConfig({
  theme: {
    colors: {
      primary: {
        DEFAULT: "#462610",
        50: "#D2793B",
        100: "#CB6E2E",
        200: "#AA5C27",
        300: "#884A1F",
        400: "#673818",
        500: "#462610",
        600: "#180D06",
        700: "#000000",
        800: "#000000",
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
