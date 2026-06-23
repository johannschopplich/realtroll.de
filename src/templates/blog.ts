// Pixels of overflow below which the toggle isn't worth showing
const OVERFLOW_BUFFER = 24;

export default function () {
  const entries = document.querySelectorAll<HTMLElement>("[data-clampable]");
  if (entries.length === 0) return;

  const controls = [...entries].flatMap((body) => {
    const toggle = body
      .closest(".blog-card")
      ?.querySelector<HTMLButtonElement>("[data-clamp-toggle]");
    return toggle ? [{ body, toggle }] : [];
  });

  for (const { body, toggle } of controls) {
    toggle.addEventListener("click", () => {
      const expanded = toggle.getAttribute("aria-expanded") !== "true";
      toggle.setAttribute("aria-expanded", String(expanded));
      body.toggleAttribute("data-overflowing", !expanded);
    });
  }

  const sync = () => {
    const rootPx =
      Number.parseFloat(getComputedStyle(document.documentElement).fontSize) ||
      16;

    for (const { body, toggle } of controls) {
      if (toggle.getAttribute("aria-expanded") === "true") continue;

      const clampPx =
        Number.parseFloat(
          getComputedStyle(body).getPropertyValue("--blog-clamp"),
        ) * rootPx;
      const overflowing =
        Number.isFinite(clampPx) &&
        body.scrollHeight > clampPx + OVERFLOW_BUFFER;
      body.toggleAttribute("data-overflowing", overflowing);
      toggle.hidden = !overflowing;
    }
  };

  const observer = new ResizeObserver(sync);
  for (const { body } of controls) observer.observe(body);
}
