// Crypto polyfill for Node.js compatibility
if (typeof global !== 'undefined' && !global.crypto) {
  try {
    const crypto = require('crypto');
    global.crypto = {
      getRandomValues: function(buffer) {
        return crypto.randomFillSync(buffer);
      }
    };
  } catch (e) {
    // Fallback if crypto module is not available
    global.crypto = {
      getRandomValues: function(buffer) {
        for (let i = 0; i < buffer.length; i++) {
          buffer[i] = Math.floor(Math.random() * 256);
        }
        return buffer;
      }
    };
  }
}

// Also fix for window.crypto if needed
if (typeof window !== 'undefined' && !window.crypto) {
  window.crypto = {
    getRandomValues: function(buffer) {
      for (let i = 0; i < buffer.length; i++) {
        buffer[i] = Math.floor(Math.random() * 256);
      }
      return buffer;
    }
  };
} 
