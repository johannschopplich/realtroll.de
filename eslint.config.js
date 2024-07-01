import antfu from "@antfu/eslint-config";

export default await antfu({
  stylistic: false,
  ignores: ["**/public/play/**", "**/plugins/*/index.js", "**/vendor/**"],
});
