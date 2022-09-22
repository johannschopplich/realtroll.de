const templates: Record<string, () => Promise<Record<string, any>>> = {
  home: () => import("./templates/home"),
};

const { template = "default" } = document.body.dataset;
templates[template]?.().then((m) => m.default?.());
