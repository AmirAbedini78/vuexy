describe('Interactive Button and Click Tests', () => {
  beforeEach(() => {
    cy.visit('/listing')
  })

  describe('Radio Button Selection Tests', () => {
    it('should test all radio button selections with visual feedback', () => {
      // Test Single Date radio button
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').should('be.visible')
      
      // Test Multi Date radio button
      cy.get('input[value="multi-date"]').click()
      cy.get('.listing-next-btn').should('be.visible')
      
      // Test Open Date radio button
      cy.get('input[value="open-date"]').click()
      cy.get('.listing-next-btn').should('be.visible')
      
      // Test Other radio button
      cy.get('input[value="other"]').click()
      cy.get('.listing-next-btn').should('be.visible')
      
      // Go back to Single Date for next tests
      cy.get('input[value="single-date"]').click()
    })

    it('should test Next button functionality', () => {
      // Select Single Date
      cy.get('input[value="single-date"]').click()
      
      // Click Next button
      cy.get('.listing-next-btn').click()
      
      // Verify wizard is loaded
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.step-item').should('have.length', 3)
      cy.get('.step-item').first().should('have.class', 'active')
    })
  })

  describe('Wizard Navigation Button Tests', () => {
    beforeEach(() => {
      // Navigate to Single Date wizard
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
    })

    it('should test Next and Previous buttons in wizard', () => {
      // Fill step 1
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Interactive Test Tour')
      
      // Click Next button
      cy.get('.next-btn-dark').click()
      
      // Verify step 2 is active
      cy.get('.step-item').eq(1).should('have.class', 'active')
      
      // Click Previous button
      cy.get('button').contains('Previous').click()
      
      // Verify step 1 is active again
      cy.get('.step-item').first().should('have.class', 'active')
      
      // Click Next again
      cy.get('.next-btn-dark').click()
      cy.get('.step-item').eq(1).should('have.class', 'active')
    })

    it('should test dynamic button additions', () => {
      // Fill step 1 and go to step 2
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Dynamic Test Tour')
      cy.get('.next-btn-dark').click()
      
      // Test Maps and Routes add button
      cy.get('button').contains('+').first().click()
      cy.get('input[placeholder*="maps and routes"]').should('have.length', 2)
      
      // Test Listing Media add button
      cy.get('button').contains('+').eq(1).click()
      cy.get('input[placeholder*="listing media"]').should('have.length', 2)
      
      // Test Promotional Video add button
      cy.get('button').contains('+').eq(2).click()
      cy.get('input[placeholder*="promotional video"]').should('have.length', 2)
      
      // Test remove buttons
      cy.get('button').contains('−').first().click()
      cy.get('input[placeholder*="maps and routes"]').should('have.length', 1)
    })
  })

  describe('Modal Button Tests', () => {
    beforeEach(() => {
      // Navigate to Single Date wizard step 3
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Quick fill steps 1 and 2
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Modal Test Tour')
      cy.get('.next-btn-dark').click()
      
      cy.get('select').first().select('Hiking')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('select').eq(2).select('Europe')
      cy.get('.next-btn-dark').click()
    })

    it('should test Itinerary Modal buttons', () => {
      // Open itinerary modal
      cy.get('button').contains('Add Itinerary/Accomodation').click()
      
      // Verify modal is open
      cy.get('.itinerary-modal-overlay').should('be.visible')
      cy.get('.itinerary-dialog-title').should('contain', 'Itinerary/Accommodations')
      
      // Test Add More Days button
      cy.get('button').contains('Add More Days').click()
      cy.get('.itinerary-sidebar-item').should('have.length', 2)
      
      // Test Done button
      cy.get('button').contains('Done').click()
      cy.get('.itinerary-modal-overlay').should('not.exist')
    })

    it('should test Special Addons Modal buttons', () => {
      // Open special addons modal
      cy.get('button').contains('Add Special Addons').click()
      
      // Verify modal is open
      cy.get('.special-addons-modal-overlay').should('be.visible')
      cy.get('.special-addons-dialog-title').should('contain', 'Special Addons')
      
      // Test Add More Addons button
      cy.get('button').contains('Add More Addons').click()
      cy.get('.special-addons-sidebar-item').should('have.length', 3)
      
      // Test Done button
      cy.get('button').contains('Done').click()
      cy.get('.special-addons-modal-overlay').should('not.exist')
    })
  })

  describe('Form Submission Button Tests', () => {
    beforeEach(() => {
      // Navigate to Single Date wizard step 3
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Quick fill steps 1 and 2
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Submit Test Tour')
      cy.get('.next-btn-dark').click()
      
      cy.get('select').first().select('Hiking')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('select').eq(2).select('Europe')
      cy.get('.next-btn-dark').click()
    })

    it('should test Submit button without terms acceptance', () => {
      // Try to submit without accepting terms
      cy.get('button').contains('Submit').click()
      
      // Should show alert about terms
      cy.on('window:alert', (text) => {
        expect(text).to.include('Please accept the terms and conditions')
      })
    })

    it('should test Submit button with terms acceptance', () => {
      // Accept terms
      cy.get('input[type="checkbox"]').check()
      
      // Submit form
      cy.get('button').contains('Submit').click()
      
      // Verify success message
      cy.on('window:alert', (text) => {
        expect(text).to.include('Single Date Listing created successfully')
      })
    })
  })

  describe('Responsive Button Tests', () => {
    it('should test buttons on mobile viewport', () => {
      cy.viewport('iphone-6')
      
      // Test radio buttons on mobile
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').should('be.visible')
      cy.get('.listing-next-btn').click()
      
      // Test wizard buttons on mobile
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.next-btn-dark').should('be.visible')
    })

    it('should test buttons on tablet viewport', () => {
      cy.viewport('ipad-2')
      
      // Test radio buttons on tablet
      cy.get('input[value="multi-date"]').click()
      cy.get('.listing-next-btn').should('be.visible')
      cy.get('.listing-next-btn').click()
      
      // Test wizard buttons on tablet
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.next-btn-dark').should('be.visible')
    })

    it('should test buttons on desktop viewport', () => {
      cy.viewport(1280, 720)
      
      // Test radio buttons on desktop
      cy.get('input[value="open-date"]').click()
      cy.get('.listing-next-btn').should('be.visible')
      cy.get('.listing-next-btn').click()
      
      // Test wizard buttons on desktop
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.next-btn-dark').should('be.visible')
    })
  })

  describe('Button State and Interaction Tests', () => {
    it('should test button hover states', () => {
      // Test Next button hover
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn')
        .trigger('mouseover')
        .should('have.css', 'background-color')
      
      // Test wizard Next button hover
      cy.get('.listing-next-btn').click()
      cy.get('.next-btn-dark')
        .trigger('mouseover')
        .should('have.css', 'background-color')
    })

    it('should test button disabled states', () => {
      // Navigate to wizard
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Try to click Next without filling required fields
      cy.get('.next-btn-dark').click()
      
      // Should stay on step 1 (button might be disabled or validation prevents progression)
      cy.get('.step-item').first().should('have.class', 'active')
    })

    it('should test button click feedback', () => {
      // Test radio button click feedback
      cy.get('input[value="single-date"]').click()
      cy.get('input[value="single-date"]').should('be.checked')
      
      // Test Next button click feedback
      cy.get('.listing-next-btn').click()
      cy.get('.custom-stepper').should('be.visible')
    })
  })

  describe('Accessibility Button Tests', () => {
    it('should test keyboard navigation for buttons', () => {
      // Navigate to wizard
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Test tab navigation
      cy.get('body').tab()
      cy.focused().should('match', 'input, button, select, textarea')
      
      // Test enter key on buttons
      cy.get('.next-btn-dark').focus().type('{enter}')
    })

    it('should test button accessibility attributes', () => {
      // Test radio buttons have proper attributes
      cy.get('input[value="single-date"]').should('have.attr', 'type', 'radio')
      
      // Test Next button has proper attributes
      cy.get('.listing-next-btn').should('be.visible')
    })
  })
}) 
