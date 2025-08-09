// Polyfill for crypto.getRandomValues in Node.js v16
if (typeof globalThis !== 'undefined' && !globalThis.crypto) {
  globalThis.crypto = {}
}

if (typeof globalThis !== 'undefined' && !globalThis.crypto.getRandomValues) {
  globalThis.crypto.getRandomValues = function(array) {
    if (typeof require !== 'undefined') {
      // Node.js environment
      try {
        const crypto = require('crypto')
        const bytes = crypto.randomBytes(array.length)
        for (let i = 0; i < array.length; i++) {
          array[i] = bytes[i]
        }
      } catch (e) {
        // Fallback to Math.random
        for (let i = 0; i < array.length; i++) {
          array[i] = Math.floor(Math.random() * 256)
        }
      }
    } else {
      // Browser environment fallback
      for (let i = 0; i < array.length; i++) {
        array[i] = Math.floor(Math.random() * 256)
      }
    }
    return array
  }
}

// Also for window object in browser
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

// Also for global object
if (typeof global !== 'undefined' && !global.crypto) {
  global.crypto = {}
}

if (typeof global !== 'undefined' && !global.crypto.getRandomValues) {
  global.crypto.getRandomValues = function(array) {
    if (typeof require !== 'undefined') {
      try {
        const crypto = require('crypto')
        const bytes = crypto.randomBytes(array.length)
        for (let i = 0; i < array.length; i++) {
          array[i] = bytes[i]
        }
      } catch (e) {
        for (let i = 0; i < array.length; i++) {
          array[i] = Math.floor(Math.random() * 256)
        }
      }
    } else {
      for (let i = 0; i < array.length; i++) {
        array[i] = Math.floor(Math.random() * 256)
      }
    }
    return array
  }
} 
