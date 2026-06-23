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
    // One-way reveal: lift the clamp for good and retire the button
    toggle.addEventListener("click", () => {
      body.removeAttribute("data-overflowing");
      toggle.remove();
    });
  }

  const sync = () => {
    const rootPx =
      Number.parseFloat(getComputedStyle(document.documentElement).fontSize) ||
      16;

    for (const { body, toggle } of controls) {
      if (!toggle.isConnected) continue;

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
