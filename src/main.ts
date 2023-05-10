const templates = {
  home: () => import("./templates/home"),
  default: undefined,
};

const template =
  (document.body.dataset.template as keyof typeof templates) ?? "default";

templates[template]?.().then((m) => m.default?.());
