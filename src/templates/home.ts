const floatingScreenshot = document.querySelector<HTMLImageElement>(
  "#floating-screenshot",
);

// Prevent redundant calls to `requestAnimationFrame`
let needsAnimationFrame = true;

let lastElement: HTMLElement | undefined;
let lastTimerId: ReturnType<typeof setInterval> | undefined;

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

const PREVIEW_CURSOR_OFFSET = 16;

function updateMouseProperties(event: MouseEvent) {
  let x = event.clientX + PREVIEW_CURSOR_OFFSET;
  let y = event.clientY + PREVIEW_CURSOR_OFFSET;

  // Flip the preview to the other side of the cursor before it would clip
  // beyond the viewport (the image is scaled, so measure its rendered size)
  const image = floatingScreenshot?.querySelector("img");
  if (image) {
    const { width, height } = image.getBoundingClientRect();
    if (width > 0 && x + width > window.innerWidth) {
      x = Math.max(event.clientX - width - PREVIEW_CURSOR_OFFSET, 0);
    }
    if (height > 0 && y + height > window.innerHeight) {
      y = Math.max(event.clientY - height - PREVIEW_CURSOR_OFFSET, 0);
    }
  }

  document.documentElement.style.setProperty("--mouseX", `${x}px`);
  document.documentElement.style.setProperty("--mouseY", `${y}px`);
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
