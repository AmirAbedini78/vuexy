describe('Simple Button Test - No API Required', () => {
  it('should test basic button interactions', () => {
    // Visit the listing page
    cy.visit('/listing')
    
    // Wait for page to load completely
    cy.wait(3000)
    
    // Test 1: Check if page loads correctly
    cy.get('.listing-title').should('be.visible')
    cy.get('.listing-title').should('contain', 'Hey')
    
    // Test 2: Check radio buttons are present
    cy.get('input[value="single-date"]').should('be.visible')
    cy.get('input[value="multi-date"]').should('be.visible')
    cy.get('input[value="open-date"]').should('be.visible')
    cy.get('input[value="other"]').should('be.visible')
    
    // Test 3: Click Single Date radio button
    cy.get('input[value="single-date"]').click()
    cy.wait(1000)
    
    // Test 4: Check Next button is visible
    cy.get('.listing-next-btn').should('be.visible')
    cy.get('.listing-next-btn').should('contain', 'Next')
    
    // Test 5: Click Next button
    cy.get('.listing-next-btn').click()
    cy.wait(2000)
    
    // Test 6: Check if wizard is loaded
    cy.get('.custom-stepper').should('be.visible')
    cy.get('.step-item').should('have.length', 3)
    
    // Test 7: Check first step is active
    cy.get('.step-item').first().should('have.class', 'active')
    
    // Test 8: Fill some basic fields
    cy.get('input[placeholder*="starting date"]').type('2024-12-01')
    cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
    cy.get('input[placeholder*="listing title"]').type('Test Tour')
    
    // Test 9: Click Next in wizard
    cy.get('.next-btn-dark').click()
    cy.wait(2000)
    
    // Test 10: Check second step is active
    cy.get('.step-item').eq(1).should('have.class', 'active')
    
    // Test 11: Go back to first step
    cy.get('button').contains('Previous').click()
    cy.wait(1000)
    cy.get('.step-item').first().should('have.class', 'active')
    
    cy.log('✅ All basic button tests completed successfully!')
  })
}) 
