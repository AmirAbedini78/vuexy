describe('Modal Functionality Test', () => {
  it('should test modal data saving and display', () => {
    // Visit the listing page
    cy.visit('/listing')
    cy.wait(3000)
    
    // Select Single Date and go to wizard
    cy.get('input[value="single-date"]').click()
    cy.get('.listing-next-btn').click()
    cy.wait(2000)
    
    // Go to Step 3
    cy.get('input[placeholder*="starting date"]').type('2024-12-01')
    cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
    cy.get('input[placeholder*="listing title"]').type('Modal Test Tour')
    cy.get('.next-btn-dark').click()
    cy.wait(2000)
    
    cy.get('select').first().select('Hiking')
    cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
    cy.get('select').eq(2).select('Europe')
    cy.get('.next-btn-dark').click()
    cy.wait(2000)
    
    // Test Itinerary Modal
    cy.log('Testing Itinerary Modal')
    cy.get('button').contains('Add Itinerary/Accomodation').click()
    cy.wait(2000)
    
    // Fill itinerary form
    cy.get('.itinerary-input').first().type('Test Day 1')
    cy.get('.itinerary-input').eq(1).type('Test Hotel')
    cy.get('.itinerary-input').eq(2).type('Test Description')
    cy.get('.itinerary-input').eq(3).type('Test Location')
    cy.get('.itinerary-input').eq(4).type('https://test.com')
    
    // Click Done
    cy.get('button').contains('Done').click()
    cy.wait(3000)
    
    // Check if itinerary is displayed
    cy.get('.itinerary-list-item').should('be.visible')
    cy.get('.itinerary-title').should('contain', 'Test Day 1')
    
    // Test Special Addons Modal
    cy.log('Testing Special Addons Modal')
    cy.get('button').contains('Add Special Addons').click()
    cy.wait(2000)
    
    // Fill addon form
    cy.get('input[placeholder="Title"]').type('Test Addon')
    cy.get('input[placeholder="Description"]').type('Test Addon Description')
    cy.get('input[placeholder="Price"]').type('150')
    
    // Click Done
    cy.get('button').contains('Done').click()
    cy.wait(3000)
    
    // Check if addon is displayed
    cy.get('.special-addon-list-item').should('be.visible')
    cy.get('.addon-title').should('contain', 'Test Addon')
    
    cy.log('✅ Modal functionality test completed!')
  })
}) 
