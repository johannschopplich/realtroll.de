const floatingScreenshot = document.querySelector<HTMLImageElement>(
  "#floating-screenshot",
);

// Prevent redundant calls to `requestAnimationFrame`
let needsAnimationFrame = true;

let lastElement: HTMLElement | undefined;
let lastTimerId: number | undefined;

export default function () {
  document.addEventListener("mousemove", (event) => {
    if (!needsAnimationFrame) return;

    requestAnimationFrame(() => {
      needsAnimationFrame = false;
      updateMouseProperties(event);
    });
  });

  if (matchMedia("(hover: hover)").matches) {
    document.addEventListener("mouseover", handleScreenshots, {
      capture: true,
    });
  }
}

function updateMouseProperties(event: MouseEvent) {
  document.documentElement.style.setProperty("--mouseX", `${event.clientX}px`);
  document.documentElement.style.setProperty("--mouseY", `${event.clientY}px`);
  needsAnimationFrame = true;
}

function handleScreenshots(event: MouseEvent) {
  if (!floatingScreenshot) return;

  const element = (event.target as HTMLElement).closest<HTMLElement>(
    "[data-screenshots]",
  );

  if (!element) {
    if (lastTimerId !== undefined) clearInterval(lastTimerId);
    floatingScreenshot.classList.add("hidden", "children:hidden");
    lastElement = undefined;
    return;
  }

  if (lastElement === element) return;

  lastElement = element;
  clearInterval(lastTimerId);

  const sources = element.dataset.screenshots?.split("|") ?? [];
  const img = floatingScreenshot.querySelector<HTMLImageElement>("img");
  const preloadImg = new Image();

  if (!img) return;

  img.src = sources[0]!;
  preloadImg.src = sources[1]!;

  let index = 1;
  lastTimerId = setInterval(() => {
    if (index === sources.length) {
      index = 0;
    }

    if (index + 1 < sources.length) {
      preloadImg.src = sources[index + 1]!;
    }

    img.src = sources[index]!;
    index++;
  }, 1250);

  floatingScreenshot.classList.remove("hidden", "children:hidden");
}
