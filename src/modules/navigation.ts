const SCROLL_THRESHOLD = 8;

export function install() {
  const nav = document.querySelector<HTMLElement>("#main-nav");
  if (!nav) return;

  const update = () => {
    nav.dataset.scrolled = window.scrollY > SCROLL_THRESHOLD ? "true" : "false";
  };

  document.addEventListener("scroll", update, { passive: true });
  update();

  setupSubpageMenu(nav);
}

function setupSubpageMenu(nav: HTMLElement) {
  const details = nav.querySelector<HTMLDetailsElement>("[data-subpage-menu]");
  const summary = details?.querySelector<HTMLElement>("summary");
  if (!details || !summary) return;

  details.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && details.open) {
      details.open = false;
      summary.focus();
    }
  });

  details.addEventListener("focusout", (event) => {
    if (!details.contains(event.relatedTarget as Node | null)) {
      details.open = false;
    }
  });

  document.addEventListener("click", (event) => {
    if (!details.contains(event.target as Node)) details.open = false;
  });

  if (matchMedia("(hover: hover) and (pointer: fine)").matches) {
    details.addEventListener("pointerenter", () => {
      details.open = true;
    });
    details.addEventListener("pointerleave", () => {
      details.open = false;
    });
  }
}
