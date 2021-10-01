let rafId = null;

function updateMouseProperties({ clientX, clientY }) {
  document.documentElement.style.setProperty("--mouseX", `${clientX}px`);
  document.documentElement.style.setProperty("--mouseY", `${clientY}px`);
  rafId = null;
}

document.addEventListener("mousemove", (event) => {
  if (rafId !== null) return;

  rafId = requestAnimationFrame(() => {
    updateMouseProperties(event);
  });
});

const cursorElement = document.querySelector("#custom-cursor");

let lastElement = null;
let lastTimerId = null;

function handleScreenshots({ target }) {
  const element = target.closest("[data-screenshots]");

  if (!element) {
    if (lastTimerId !== null) clearInterval(lastTimerId);
    cursorElement.setAttribute("hidden", "");
    lastElement = null;
    return;
  }

  if (lastElement === element) return;

  lastElement = element;
  clearInterval(lastTimerId);

  const sources = element.dataset.screenshots.split("|");
  const preloadImg = new Image();

  cursorElement.src = sources[0];
  preloadImg.src = sources[1];

  let index = 1;
  lastTimerId = setInterval(() => {
    if (index === sources.length) {
      index = 0;
    }

    if (index + 1 < sources.length) {
      preloadImg.src = sources[index + 1];
    }

    cursorElement.src = sources[index];
    index++;
  }, 1250);

  cursorElement.removeAttribute("hidden");
}

if (cursorElement && window.matchMedia("(hover: hover)").matches) {
  document.addEventListener("mouseover", handleScreenshots, { capture: true });
}
