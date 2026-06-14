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

  const close = () => {
    details.open = false;
  };

  details.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && details.open) {
      close();
      summary.focus();
    }
  });

  // Guard against closing before a tapped link navigates (touch taps don't focus)
  details.addEventListener("focusout", (event) => {
    if (event.relatedTarget && !details.contains(event.relatedTarget as Node)) {
      close();
    }
  });

  document.addEventListener("click", (event) => {
    if (!details.contains(event.target as Node)) close();
  });

  if (matchMedia("(hover: hover) and (pointer: fine)").matches) {
    // Hover owns open/close here; don't let a pointer click toggle fight it
    summary.addEventListener("click", (event) => {
      if (event.detail > 0) event.preventDefault();
    });
    details.addEventListener("pointerenter", () => {
      details.open = true;
    });
    details.addEventListener("pointerleave", close);
  }
}
