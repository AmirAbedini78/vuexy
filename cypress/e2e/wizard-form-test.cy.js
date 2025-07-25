describe('Wizard Form Test - Complete Flow', () => {
  it('should complete the entire wizard form flow', () => {
    // Visit the listing page
    cy.visit('/listing')
    cy.wait(3000)
    
    // Step 1: Select Single Date and go to wizard
    cy.get('input[value="single-date"]').click()
    cy.get('.listing-next-btn').click()
    cy.wait(2000)
    
    // Verify wizard is loaded
    cy.get('.custom-stepper').should('be.visible')
    cy.get('.step-item').should('have.length', 3)
    cy.get('.step-item').first().should('have.class', 'active')
    
    // Step 1: Fill basic information
    cy.log('Filling Step 1 - Basic Information')
    cy.get('input[placeholder*="starting date"]').type('2024-12-01')
    cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
    cy.get('input[placeholder*="listing title"]').type('Complete Test Tour')
    cy.get('input[placeholder*="listing description"]').type('A comprehensive test tour description')
    cy.get('input[placeholder*="price"]').type('1500')
    cy.get('input[placeholder*="min capacity"]').type('2')
    cy.get('input[placeholder*="max capacity"]').type('10')
    cy.get('input[placeholder*="subtitle"]').type('Adventure Test Tour')
    cy.get('select').first().select('Hiking')
    cy.get('select').eq(1).select('Intermediate')
    cy.get('select').eq(2).select('Moderate')
    
    // Go to Step 2
    cy.get('.next-btn-dark').click()
    cy.wait(2000)
    cy.get('.step-item').eq(1).should('have.class', 'active')
    
    // Step 2: Fill detailed information
    cy.log('Filling Step 2 - Detailed Information')
    cy.get('input[placeholder*="activities included"]').type('Hiking, Camping, Photography')
    cy.get('select').first().select('English')
    cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com/test')
    cy.get('select').eq(2).select('Europe')
    cy.get('input[placeholder*="listing media"]').type('https://example.com/image1.jpg')
    cy.get('input[placeholder*="promotional video"]').type('https://youtube.com/watch?v=test')
    cy.get('textarea[placeholder*="what\'s included"]').type('All equipment, guide, meals')
    cy.get('textarea[placeholder*="what\'s not included"]').type('Personal items, insurance')
    cy.get('textarea[placeholder*="additional notes"]').type('Bring warm clothes')
    cy.get('textarea[placeholder*="provider\'s FAQ"]').type('Common questions and answers')
    
    // Go to Step 3
    cy.get('.next-btn-dark').click()
    cy.wait(2000)
    cy.get('.step-item').eq(2).should('have.class', 'active')
    
    // Step 3: Test Itinerary Modal
    cy.log('Testing Itinerary Modal')
    cy.get('button').contains('Add Itinerary/Accomodation').click()
    cy.wait(2000)
    
    // Verify modal is open
    cy.get('.itinerary-modal-overlay').should('be.visible')
    cy.get('.itinerary-dialog-title').should('contain', 'Itinerary/Accommodations')
    
    // Fill itinerary form
    cy.get('.itinerary-input').first().type('Day 1 Adventure')
    cy.get('.itinerary-input').eq(1).type('Mountain Lodge')
    cy.get('.itinerary-input').eq(2).type('Hiking adventure in the mountains')
    cy.get('.itinerary-input').eq(3).type('Mountain Peak, Switzerland')
    cy.get('.itinerary-input').eq(4).type('https://maps.google.com/lodge')
    
    // Add more days
    cy.get('button').contains('Add More Days').click()
    cy.wait(1000)
    cy.get('.itinerary-sidebar-item').should('have.length', 2)
    
    // Close modal
    cy.get('button').contains('Done').click()
    cy.wait(2000)
    cy.get('.itinerary-modal-overlay').should('not.exist')
    
    // Verify itinerary is listed
    cy.get('.itinerary-list-item').should('be.visible')
    cy.get('.itinerary-title').should('contain', 'Day 1 Adventure')
    
    // Step 3: Test Special Addons Modal
    cy.log('Testing Special Addons Modal')
    cy.get('button').contains('Add Special Addons').click()
    cy.wait(2000)
    
    // Verify modal is open
    cy.get('.special-addons-modal-overlay').should('be.visible')
    cy.get('.special-addons-dialog-title').should('contain', 'Special Addons')
    
    // Fill addon form
    cy.get('input[placeholder*="addon title"]').first().type('Premium Equipment')
    cy.get('input[placeholder*="addon description"]').first().type('High-quality hiking equipment')
    cy.get('input[placeholder*="addon price"]').first().type('200')
    
    // Add more addons
    cy.get('button').contains('Add More Addons').click()
    cy.wait(1000)
    cy.get('.special-addons-sidebar-item').should('have.length', 3)
    
    // Close modal
    cy.get('button').contains('Done').click()
    cy.wait(2000)
    cy.get('.special-addons-modal-overlay').should('not.exist')
    
    // Verify addon is listed
    cy.get('.special-addon-list-item').should('be.visible')
    cy.get('.addon-title').should('contain', 'Premium Equipment')
    
    // Step 3: Fill personal policies
    cy.log('Filling Personal Policies')
    cy.get('input[value="personal"]').click()
    cy.get('textarea[placeholder*="personal policies"]').type('Our safety policies and guidelines')
    
    // Submit form
    cy.log('Submitting Form')
    cy.get('button').contains('Submit').click()
    cy.wait(3000)
    
    // Verify success
    cy.on('window:alert', (text) => {
      expect(text).to.include('Single Date Listing created successfully')
    })
    
    cy.log('✅ Complete wizard form test passed!')
  })
}) 
