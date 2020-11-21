function throttleFn (delay, fn) {
  let lastCall = 0

  return function (...args) {
    const now = Date.now()
    if (now - lastCall < delay) return
    lastCall = now
    return fn(...args)
  }
}

const handleMouse = throttleFn(25, ({ clientX, clientY }) => {
  document.documentElement.style.setProperty('--mouseX', `${clientX}px`)
  document.documentElement.style.setProperty('--mouseY', `${clientY}px`)
})

window.addEventListener('mousemove', handleMouse)

const cursorImg = document.querySelector('#custom-cursor')

function main () {
  if (!cursorImg) return

  let lastElement
  let lastTimerId

  document.body.addEventListener('mouseover', event => {
    const element = event.target.closest('[data-screenshots]')

    if (!element) {
      cursorImg.setAttribute('hidden', '')
      if (lastElement) lastElement = null
      if (lastTimerId) clearInterval(lastTimerId)
      return
    }

    if (lastElement !== element) {
      lastElement = element
      clearInterval(lastTimerId)

      const sources = element.dataset.screenshots.split('|')

      let index = 1
      cursorImg.src = sources[0]
      lastTimerId = setInterval(() => {
        if (index === sources.length) index = 0
        cursorImg.src = sources[index]
        index++
      }, 1000)

      cursorImg.removeAttribute('hidden')
    }
  }, { capture: true })
}

main()
