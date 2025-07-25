describe('Simple Interactive Button Test', () => {
  it('should test all buttons step by step', () => {
    // Visit the listing page
    cy.visit('/listing')
    
    // Wait for page to load
    cy.wait(2000)
    
    // Test 1: Radio Button Selection
    cy.log('Testing Radio Button Selection...')
    cy.get('input[value="single-date"]').click()
    cy.wait(1000)
    
    // Test 2: Next Button
    cy.log('Testing Next Button...')
    cy.get('.listing-next-btn').click()
    cy.wait(2000)
    
    // Test 3: Fill Step 1
    cy.log('Filling Step 1...')
    cy.get('input[placeholder*="starting date"]').type('2024-12-01')
    cy.wait(500)
    cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
    cy.wait(500)
    cy.get('input[placeholder*="listing title"]').type('Interactive Test Tour')
    cy.wait(500)
    
    // Test 4: Next Button in Wizard
    cy.log('Testing Next Button in Wizard...')
    cy.get('.next-btn-dark').click()
    cy.wait(2000)
    
    // Test 5: Fill Step 2
    cy.log('Filling Step 2...')
    cy.get('select').first().select('Hiking')
    cy.wait(500)
    cy.get('select').eq(1).select('English')
    cy.wait(500)
    cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
    cy.wait(500)
    cy.get('select').eq(2).select('Europe')
    cy.wait(500)
    
    // Test 6: Add More Fields Button
    cy.log('Testing Add More Fields Button...')
    cy.get('button').contains('+').first().click()
    cy.wait(1000)
    
    // Test 7: Next Button to Step 3
    cy.log('Moving to Step 3...')
    cy.get('.next-btn-dark').click()
    cy.wait(2000)
    
    // Test 8: Modal Buttons
    cy.log('Testing Modal Buttons...')
    cy.get('button').contains('Add Itinerary/Accomodation').click()
    cy.wait(2000)
    
    // Test 9: Modal Navigation
    cy.log('Testing Modal Navigation...')
    cy.get('button').contains('Add More Days').click()
    cy.wait(1000)
    cy.get('button').contains('Done').click()
    cy.wait(2000)
    
    // Test 10: Special Addons Modal
    cy.log('Testing Special Addons Modal...')
    cy.get('button').contains('Add Special Addons').click()
    cy.wait(2000)
    cy.get('button').contains('Add More Addons').click()
    cy.wait(1000)
    cy.get('button').contains('Done').click()
    cy.wait(2000)
    
    // Test 11: Form Submission
    cy.log('Testing Form Submission...')
    cy.get('input[type="checkbox"]').check()
    cy.wait(500)
    cy.get('button').contains('Submit').click()
    cy.wait(2000)
    
    cy.log('All button tests completed!')
  })
}) 
