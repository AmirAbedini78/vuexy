describe('Homepage', () => {
  beforeEach(() => {
    cy.visit('/')
  })

  it('should load the homepage successfully', () => {
    cy.get('body').should('be.visible')
    cy.url().should('eq', Cypress.config().baseUrl + '/')
  })

  it('should have a title', () => {
    cy.title().should('not.be.empty')
  })

  it('should load without console errors', () => {
    cy.window().then((win) => {
      cy.spy(win.console, 'error').as('consoleError')
    })
    
    cy.get('@consoleError').should('not.be.called')
  })
}) 
