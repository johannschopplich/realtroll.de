const CYCLE_INTERVAL = 1250;

export default function () {
  if (!matchMedia("(hover: hover)").matches) return;

  const prefersReducedMotion = matchMedia("(prefers-reduced-motion: reduce)");
  const panel = document.querySelector<HTMLElement>("#screenshot-showcase");
  const image = panel?.querySelector("img");
  const titleElement = panel?.querySelector<HTMLElement>(
    "[data-showcase-title]",
  );
  const tilesElement = panel?.querySelector<HTMLElement>(
    "[data-showcase-tiles]",
  );
  if (!panel || !image || !titleElement || !tilesElement) return;

  const preloadImage = new Image();
  let lastElement: HTMLElement | undefined;
  let timerId: ReturnType<typeof setInterval> | undefined;

  function renderTiles(count: number, activeIndex: number) {
    tilesElement!.replaceChildren(
      ...Array.from({ length: count }, (_, tileIndex) => {
        const tile = document.createElement("span");
        Object.assign(tile.style, {
          width: "0.375rem",
          height: "0.375rem",
          background:
            tileIndex === activeIndex ? "#fff" : "rgb(255 255 255 / 0.4)",
        });
        return tile;
      }),
    );
  }

  function show(element: HTMLElement) {
    const sources =
      element.dataset.screenshots?.split("|").filter(Boolean) ?? [];
    if (sources.length === 0) return;

    clearInterval(timerId);

    titleElement!.textContent = element.dataset.title ?? "";

    let index = 0;
    image!.src = sources[0]!;
    renderTiles(sources.length, 0);
    if (sources[1]) preloadImage.src = sources[1];

    // Auto-advancing the slideshow is motion, too
    if (!prefersReducedMotion.matches && sources.length > 1) {
      timerId = setInterval(() => {
        index = (index + 1) % sources.length;
        image!.src = sources[index]!;
        renderTiles(sources.length, index);
        preloadImage.src = sources[(index + 1) % sources.length]!;
      }, CYCLE_INTERVAL);
    }

    // Anchor the panel to the opposite side of the hovered tile so it never
    // covers the card the cursor is on
    const rect = element.getBoundingClientRect();
    if (rect.left + rect.width / 2 > innerWidth / 2) {
      panel!.style.left = "var(--spacing-lg)";
      panel!.style.right = "auto";
    } else {
      panel!.style.left = "auto";
      panel!.style.right = "var(--spacing-lg)";
    }

    panel!.classList.remove("invisible", "opacity-0", "translate-y-4");
  }

  function hide() {
    clearInterval(timerId);
    panel!.classList.add("invisible", "opacity-0", "translate-y-4");
  }

  document.addEventListener(
    "mouseover",
    (event) => {
      const element = (event.target as HTMLElement).closest<HTMLElement>(
        "[data-screenshots]",
      );

      if (!element) {
        if (lastElement) {
          lastElement = undefined;
          hide();
        }
        return;
      }

      if (element === lastElement) return;
      lastElement = element;
      show(element);
    },
    { capture: true },
  );
}
