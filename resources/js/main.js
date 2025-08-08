import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'
import { createApp } from 'vue'

// Import crypto polyfill to fix crypto.getRandomValues error
import '@/polyfills'

// Add process polyfill for browser environment
if (typeof window !== 'undefined' && !window.process) {
  window.process = {
    env: {},
    client: true,
    server: false
  }
}

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'

// Initialize CSRF
const initCsrf = async () => {
  try {
    await fetch(`${import.meta.env.VITE_API_BASE_URL}/sanctum/csrf-cookie`, {
      credentials: 'include',
    })
    console.log('CSRF token initialized')
  } catch (err) {
    console.error('Failed to initialize CSRF token:', err)
  }
}

// MSW setup removed to fix development server error
// if (import.meta.env.MODE === 'development') {
//   import('./handlers').then(({ handlers }) => {
//     import('msw').then(({ setupWorker }) => {
//       const worker = setupWorker(...handlers)
//       worker.start()
//     })
//   })
// }

// Create vue app
const app = createApp(App)

// Register plugins
registerPlugins(app)

// Initialize CSRF before mounting
initCsrf().then(() => {
  // Mount vue app
  app.mount('#app')
})
