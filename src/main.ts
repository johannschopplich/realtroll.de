const templates: Record<string, () => Promise<Record<string, any>>> = {
  home: () => import("./templates/home"),
};

templates[document.body.dataset.template ?? ""]?.().then((m) => m.default());
