import { defineConfig, presetUno } from "unocss";
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
  },

  presets: [presetUno(), presetDue()],
});
