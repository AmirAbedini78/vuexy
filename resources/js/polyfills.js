// Polyfill for crypto.getRandomValues in Node.js v16
if (typeof global !== 'undefined' && !global.crypto) {
  global.crypto = {}
}

if (typeof global !== 'undefined' && !global.crypto.getRandomValues) {
  global.crypto.getRandomValues = function(array) {
    for (let i = 0; i < array.length; i++) {
      array[i] = Math.floor(Math.random() * 256)
    }
    return array
  }
}

// Also for window object
if (typeof window !== 'undefined' && !window.crypto) {
  window.crypto = {}
}

if (typeof window !== 'undefined' && !window.crypto.getRandomValues) {
  window.crypto.getRandomValues = function(array) {
    for (let i = 0; i < array.length; i++) {
      array[i] = Math.floor(Math.random() * 256)
    }
    return array
  }
} 
