// @ts-check
import antfu from "@antfu/eslint-config";

export default antfu({
  stylistic: false,
  ignores: ["**/public/play/**", "**/plugins/*/index.js", "**/vendor/**"],
});
