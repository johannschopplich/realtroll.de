const CYCLE_INTERVAL = 1250;

const prefersReducedMotion = matchMedia("(prefers-reduced-motion: reduce)");

export default function () {
  if (!matchMedia("(hover: hover)").matches) return;

  const panel = document.querySelector<HTMLElement>("#screenshot-showcase");
  const image = panel?.querySelector("img");
  const titleElement = panel?.querySelector<HTMLElement>(
    "[data-showcase-title]",
  );
  const dotsElement = panel?.querySelector<HTMLElement>("[data-showcase-dots]");
  if (!panel || !image || !titleElement || !dotsElement) return;

  // Screenshots are 320px wide; scale to whole device pixels to keep the
  // pixel art crisp (e.g. retina DPR 2 → 3 device pixels per game pixel)
  image.style.width = `${
    320 * (Number.isInteger(devicePixelRatio * 1.5) ? 1.5 : 2)
  }px`;

  const preloadImage = new Image();
  let lastElement: HTMLElement | undefined;
  let timerId: ReturnType<typeof setInterval> | undefined;

  function renderDots(count: number, activeIndex: number) {
    dotsElement!.replaceChildren(
      ...Array.from({ length: count }, (_, dotIndex) => {
        const dot = document.createElement("span");
        Object.assign(dot.style, {
          width: "0.5rem",
          height: "0.5rem",
          background:
            dotIndex === activeIndex
              ? "var(--un-color-primary-700)"
              : "var(--un-color-primary-200)",
        });
        return dot;
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
    renderDots(sources.length, 0);
    if (sources[1]) preloadImage.src = sources[1];

    // Auto-advancing the slideshow is motion, too
    if (!prefersReducedMotion.matches && sources.length > 1) {
      timerId = setInterval(() => {
        index = (index + 1) % sources.length;
        image!.src = sources[index]!;
        renderDots(sources.length, index);
        preloadImage.src = sources[(index + 1) % sources.length]!;
      }, CYCLE_INTERVAL);
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
