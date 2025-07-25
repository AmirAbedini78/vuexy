import { defineConfig } from "cypress";

export default defineConfig({
  e2e: {
    baseUrl: 'http://vuexy.test', // Added based on .env APP_URL
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
    viewportWidth: 1280, // Added for consistent test environment
    viewportHeight: 720, // Added for consistent test environment
    video: false, // Disabled video recording for faster runs
    screenshotOnRunFailure: true, // Enabled screenshots on failure
    defaultCommandTimeout: 10000, // Increased default timeout
    requestTimeout: 10000, // Increased request timeout
    responseTimeout: 10000, // Increased response timeout
    chromeWebSecurity: false, // Disable web security for testing
    experimentalModifyObstructiveThirdPartyCode: true, // Handle third-party code
    experimentalSessionAndOrigin: true, // Better session handling
    // Chrome-specific settings
    chrome: {
      args: [
        '--disable-web-security',
        '--disable-features=VizDisplayCompositor',
        '--disable-dev-shm-usage',
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-gpu',
        '--disable-background-timer-throttling',
        '--disable-backgrounding-occluded-windows',
        '--disable-renderer-backgrounding',
        '--disable-features=TranslateUI',
        '--disable-ipc-flooding-protection',
      ],
    },
  },
});
