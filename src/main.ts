const templates = {
  home: () => import("./templates/home"),
  default: undefined,
};

const template = document.body.dataset.template as
  | keyof typeof templates
  | undefined;

templates[template ?? "default"]?.().then((m) => m.default?.());
