// Development script with crypto polyfill
// Polyfill for Node.js v16 crypto issue
if (!global.crypto) {
  global.crypto = {}
}
if (!global.crypto.getRandomValues) {
  global.crypto.getRandomValues = function(array) {
    for (let i = 0; i < array.length; i++) {
      array[i] = Math.floor(Math.random() * 256)
    }
    return array
  }
}

// Start Vite
import('vite').then(({ createServer }) => {
  createServer()
}) 
