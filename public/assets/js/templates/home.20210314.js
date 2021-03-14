let rafId = null

function updateMouseProperties ({ clientX, clientY }) {
  document.documentElement.style.setProperty('--mouseX', `${clientX}px`)
  document.documentElement.style.setProperty('--mouseY', `${clientY}px`)
  rafId = null
}

document.addEventListener('mousemove', event => {
  if (rafId !== null) return

  rafId = requestAnimationFrame(() => {
    updateMouseProperties(event)
  })
})

const cursorElement = document.querySelector('#custom-cursor')
let lastElement = null
let lastTimerId = null

function showScreenshot (event) {
  const element = event.target.closest('[data-screenshots]')

  if (!element) {
    if (lastTimerId !== null) clearInterval(lastTimerId)
    cursorElement.setAttribute('hidden', '')
    lastElement = null
    return
  }

  if (lastElement !== element) {
    lastElement = element
    clearInterval(lastTimerId)

    const sources = element.dataset.screenshots.split('|')
    const preloadImg = new Image()

    cursorElement.src = sources[0]
    preloadImg.src = sources[1]

    let index = 1
    lastTimerId = setInterval(() => {
      if (index === sources.length) index = 0
      cursorElement.src = sources[index]
      preloadImg.src = sources[index + 1]
      index++
    }, 1000)

    cursorElement.removeAttribute('hidden')
  }
}

if (
  cursorElement &&
  window.matchMedia('(hover: hover)').matches
) {
  document.addEventListener('mouseover', event => {
    if (rafId !== null) return
    showScreenshot(event)
  }, { capture: true })
}
