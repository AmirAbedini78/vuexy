describe('Login Test', () => {
  it('should login successfully', () => {
    // Visit login page
    cy.visit('/login')
    
    // Wait for page to load
    cy.wait(2000)
    
    // Fill login form
    cy.get('input[type="email"]').type('test@example.com')
    cy.get('input[type="password"]').type('password123')
    
    // Click login button
    cy.get('button[type="submit"]').click()
    
    // Wait for login to complete
    cy.wait(3000)
    
    // Verify we're logged in by checking if we can access protected page
    cy.visit('/listing')
    cy.wait(2000)
    
    // Should be able to see the listing page content
    cy.get('.listing-title').should('be.visible')
  })
}) 
