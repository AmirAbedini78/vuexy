// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

// Custom command to wait for Vue.js to be ready
Cypress.Commands.add('waitForVue', () => {
  cy.window().then((win) => {
    // Wait for Vue to be mounted
    cy.wait(1000)
  })
})

// Custom command to check if element is visible and clickable
Cypress.Commands.add('clickIfVisible', (selector) => {
  cy.get('body').then(($body) => {
    if ($body.find(selector).length > 0) {
      cy.get(selector).should('be.visible').click()
    }
  })
})

// Custom command to handle API requests with authentication
Cypress.Commands.add('apiRequest', (method, url, body = null, headers = {}) => {
  const defaultHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    ...headers
  }

  return cy.request({
    method,
    url: `http://vuexy.test/api${url}`,
    body,
    headers: defaultHeaders,
    failOnStatusCode: false
  })
})

// Custom command to check for console errors
Cypress.Commands.add('checkForConsoleErrors', () => {
  cy.window().then((win) => {
    cy.spy(win.console, 'error').as('consoleError')
    cy.spy(win.console, 'warn').as('consoleWarn')
  })
})
