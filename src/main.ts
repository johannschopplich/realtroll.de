const templates = {
  home: () => import("./templates/home"),
  default: undefined,
};

type Template = keyof typeof templates;

const template = (document.body.dataset.template as Template) ?? "default";

templates[template]?.().then((m) => m.default?.());
