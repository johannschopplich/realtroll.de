const SCROLL_THRESHOLD = 8;

export function install() {
  const nav = document.querySelector<HTMLElement>("#main-nav");
  if (!nav) return;

  const update = () => {
    nav.dataset.scrolled = window.scrollY > SCROLL_THRESHOLD ? "true" : "false";
  };

  document.addEventListener("scroll", update, { passive: true });
  update();
}
