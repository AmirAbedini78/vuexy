describe('Wizard Forms - Single Date, Multi Date, Open Date', () => {
  beforeEach(() => {
    cy.visit('/listing')
  })

  describe('Single Date Wizard Form', () => {
    it('should load single date wizard form', () => {
      // Select Single Date option by clicking on the radio button with value "single-date"
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Verify wizard is loaded
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.step-item').should('have.length', 3)
      cy.get('.step-item').first().should('have.class', 'active')
    })

    it('should complete Step 1 - Basic Information', () => {
      // Navigate to single date wizard
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Fill Step 1 fields
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Amazing Adventure Tour')
      cy.get('textarea[placeholder*="listing description"]').type('An incredible adventure experience')
      cy.get('input[placeholder*="price"]').type('500')
      cy.get('input[placeholder*="min capacity"]').type('2')
      cy.get('input[placeholder*="max capacity"]').type('10')
      cy.get('input[placeholder*="subtitle"]').type('Epic Journey')
      
      // Select experience level
      cy.get('input[value="moderate"]').first().click()
      
      // Select fitness level
      cy.get('input[value="moderate"]').last().click()
      
      // Go to next step
      cy.get('.next-btn-dark').click()
      
      // Verify we're on step 2
      cy.get('.step-item').eq(1).should('have.class', 'active')
    })

    it('should complete Step 2 - Detailed Information', () => {
      // Navigate to single date wizard and complete step 1
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Quick fill step 1
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Test Tour')
      cy.get('.next-btn-dark').click()
      
      // Fill Step 2 fields
      cy.get('select').first().select('Hiking')
      cy.get('select').eq(1).select('English')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('select').eq(2).select('Europe')
      
      // Add more maps and routes
      cy.get('button').contains('+').first().click()
      cy.get('input[placeholder*="maps and routes"]').eq(1).type('https://maps.apple.com')
      
      // Add listing media
      cy.get('input[placeholder*="listing media"]').type('https://example.com/image1.jpg')
      cy.get('button').contains('+').eq(1).click()
      cy.get('input[placeholder*="listing media"]').eq(1).type('https://example.com/image2.jpg')
      
      // Add promotional video
      cy.get('input[placeholder*="promotional video"]').type('https://youtube.com/watch?v=test')
      
      // Fill text areas
      cy.get('textarea[placeholder*="what\'s included"]').type('Accommodation, meals, guide')
      cy.get('textarea[placeholder*="what\'s not included"]').type('Flights, insurance')
      cy.get('textarea[placeholder*="additional notes"]').type('Bring comfortable shoes')
      cy.get('textarea[placeholder*="provider\'s FAQ"]').type('Common questions and answers')
      
      // Go to next step
      cy.get('.next-btn-dark').click()
      
      // Verify we're on step 3
      cy.get('.step-item').eq(2).should('have.class', 'active')
    })

    it('should complete Step 3 - Logistics and submit', () => {
      // Navigate to single date wizard and complete previous steps
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Quick fill steps 1 and 2
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Test Tour')
      cy.get('.next-btn-dark').click()
      
      cy.get('select').first().select('Hiking')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('select').eq(2).select('Europe')
      cy.get('.next-btn-dark').click()
      
      // Fill Step 3 fields
      cy.get('input[placeholder*="personal policies"]').type('Cancellation policy details')
      cy.get('input[placeholder*="personal policies text"]').type('Detailed policy information')
      
      // Accept terms
      cy.get('input[type="checkbox"]').check()
      
      // Submit form
      cy.get('button').contains('Submit').click()
      
      // Verify success message or redirect
      cy.on('window:alert', (text) => {
        expect(text).to.include('Single Date Listing created successfully')
      })
    })

    it('should test Itinerary/Accommodation modal', () => {
      // Navigate to single date wizard and go to step 3
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Quick fill steps 1 and 2
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Test Tour')
      cy.get('.next-btn-dark').click()
      
      cy.get('select').first().select('Hiking')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('select').eq(2).select('Europe')
      cy.get('.next-btn-dark').click()
      
      // Open itinerary modal
      cy.get('button').contains('Add Itinerary/Accomodation').click()
      
      // Verify modal is open
      cy.get('.itinerary-modal-overlay').should('be.visible')
      cy.get('.itinerary-dialog-title').should('contain', 'Itinerary/Accommodations')
      
      // Fill day 1 itinerary
      cy.get('input[placeholder="Itinerary Title"]').type('Day 1 - Arrival')
      cy.get('input[placeholder="Add accommodation here"]').type('Hotel Central')
      cy.get('input[placeholder="Itinerary Description"]').type('Welcome and orientation')
      cy.get('input[placeholder="Add exact accommodation location"]').type('Paris, France')
      cy.get('input[placeholder="Add your link here"]').type('https://hotel-central.com')
      
      // Add more days
      cy.get('button').contains('Add More Days').click()
      
      // Switch to day 2
      cy.get('.itinerary-sidebar-item').eq(1).click()
      
      // Fill day 2 itinerary
      cy.get('input[placeholder="Itinerary Title"]').type('Day 2 - Adventure')
      cy.get('input[placeholder="Add accommodation here"]').type('Mountain Lodge')
      cy.get('input[placeholder="Itinerary Description"]').type('Hiking adventure')
      cy.get('input[placeholder="Add exact accommodation location"]').type('Alps, Switzerland')
      
      // Close modal
      cy.get('button').contains('Done').click()
      
      // Verify modal is closed
      cy.get('.itinerary-modal-overlay').should('not.exist')
    })

    it('should test Special Addons modal', () => {
      // Navigate to single date wizard and go to step 3
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Quick fill steps 1 and 2
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Test Tour')
      cy.get('.next-btn-dark').click()
      
      cy.get('select').first().select('Hiking')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('select').eq(2).select('Europe')
      cy.get('.next-btn-dark').click()
      
      // Open special addons modal
      cy.get('button').contains('Add Special Addons').click()
      
      // Verify modal is open
      cy.get('.special-addons-modal-overlay').should('be.visible')
      cy.get('.special-addons-dialog-title').should('contain', 'Special Addons')
      
      // Fill addon 1
      cy.get('input[placeholder="Title"]').type('Equipment Rental')
      cy.get('input[placeholder="Price"]').type('50')
      cy.get('input[placeholder="Description"]').type('Professional hiking equipment')
      
      // Add more addons
      cy.get('button').contains('Add More Addons').click()
      
      // Switch to addon 2
      cy.get('.special-addons-sidebar-item').eq(1).click()
      
      // Fill addon 2
      cy.get('input[placeholder="Title"]').type('Photography Package')
      cy.get('input[placeholder="Price"]').type('75')
      cy.get('input[placeholder="Description"]').type('Professional photos of your adventure')
      
      // Close modal
      cy.get('button').contains('Done').click()
      
      // Verify modal is closed
      cy.get('.special-addons-modal-overlay').should('not.exist')
    })
  })

  describe('Multi Date Wizard Form', () => {
    it('should load multi date wizard form', () => {
      // Select Multi Date option
      cy.get('input[value="multi-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Verify wizard is loaded
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.step-item').should('have.length', 3)
      cy.get('.step-item').first().should('have.class', 'active')
    })

    it('should complete Multi Date wizard form', () => {
      // Navigate to multi date wizard
      cy.get('input[value="multi-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Step 1 - Basic Information
      cy.get('input[placeholder*="listing title"]').type('Multi Date Adventure')
      cy.get('textarea[placeholder*="listing description"]').type('Flexible adventure dates')
      cy.get('select').first().select('Europe')
      cy.get('select').eq(1).select('English')
      cy.get('input[placeholder*="subtitle"]').type('Flexible Dates')
      cy.get('select').eq(2).select('Hiking')
      cy.get('input[value="moderate"]').first().click()
      cy.get('input[value="moderate"]').last().click()
      cy.get('.next-btn-dark').click()
      
      // Step 2 - Date Range
      cy.get('input[placeholder*="start date"]').type('2024-01-01')
      cy.get('input[placeholder*="end date"]').type('2024-12-31')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('input[placeholder*="listing media"]').type('https://example.com/image.jpg')
      cy.get('input[placeholder*="promotional video"]').type('https://youtube.com/watch?v=test')
      cy.get('textarea[placeholder*="what\'s included"]').type('Accommodation, meals')
      cy.get('textarea[placeholder*="what\'s not included"]').type('Flights')
      cy.get('textarea[placeholder*="additional notes"]').type('Flexible booking')
      cy.get('textarea[placeholder*="provider\'s FAQ"]').type('FAQ content')
      cy.get('.next-btn-dark').click()
      
      // Step 3 - Pricing & Details
      cy.get('input[placeholder*="price"]').type('400')
      cy.get('input[placeholder*="max participants"]').type('8')
      cy.get('textarea[placeholder*="requirements"]').type('Good fitness level')
      cy.get('input[type="checkbox"]').check()
      cy.get('button').contains('Submit').click()
      
      // Verify success
      cy.on('window:alert', (text) => {
        expect(text).to.include('Multi Date Listing created successfully')
      })
    })
  })

  describe('Open Date Wizard Form', () => {
    it('should load open date wizard form', () => {
      // Select Open Date option
      cy.get('input[value="open-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Verify wizard is loaded
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.step-item').should('have.length', 3)
      cy.get('.step-item').first().should('have.class', 'active')
    })

    it('should complete Open Date wizard form', () => {
      // Navigate to open date wizard
      cy.get('input[value="open-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Step 1 - Basic Information
      cy.get('input[placeholder*="listing title"]').type('Open Date Adventure')
      cy.get('input[placeholder*="subtitle"]').type('Any Time Adventure')
      cy.get('textarea[placeholder*="description"]').type('Book anytime adventure')
      cy.get('select').first().select('Hiking')
      cy.get('select').eq(1).select('Europe')
      cy.get('select').eq(2).select('English')
      cy.get('input[value="moderate"]').first().click()
      cy.get('input[value="moderate"]').last().click()
      cy.get('.next-btn-dark').click()
      
      // Step 2 - Availability
      cy.get('input[placeholder*="min advance notice"]').type('7')
      cy.get('input[placeholder*="max advance booking"]').type('365')
      cy.get('input[placeholder*="maps and routes"]').type('https://maps.google.com')
      cy.get('input[placeholder*="listing media"]').type('https://example.com/image.jpg')
      cy.get('input[placeholder*="promotional video"]').type('https://youtube.com/watch?v=test')
      cy.get('textarea[placeholder*="what\'s included"]').type('Accommodation, meals')
      cy.get('textarea[placeholder*="what\'s not included"]').type('Flights')
      cy.get('textarea[placeholder*="additional notes"]').type('Flexible booking')
      cy.get('textarea[placeholder*="provider\'s FAQ"]').type('FAQ content')
      cy.get('.next-btn-dark').click()
      
      // Step 3 - Pricing & Details
      cy.get('input[placeholder*="price"]').type('350')
      cy.get('input[placeholder*="max participants"]').type('6')
      cy.get('textarea[placeholder*="requirements"]').type('Good fitness level')
      cy.get('input[type="checkbox"]').check()
      cy.get('button').contains('Submit').click()
      
      // Verify success
      cy.on('window:alert', (text) => {
        expect(text).to.include('Open Date Listing created successfully')
      })
    })
  })

  describe('Form Validation', () => {
    it('should validate required fields in Single Date form', () => {
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Try to submit without filling required fields
      cy.get('.next-btn-dark').click()
      
      // Should stay on step 1
      cy.get('.step-item').first().should('have.class', 'active')
    })

    it('should validate terms acceptance', () => {
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Fill required fields
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Test Tour')
      
      // Go through all steps without accepting terms
      cy.get('.next-btn-dark').click()
      cy.get('select').first().select('Hiking')
      cy.get('.next-btn-dark').click()
      
      // Try to submit without accepting terms
      cy.get('button').contains('Submit').click()
      
      // Should show alert about terms
      cy.on('window:alert', (text) => {
        expect(text).to.include('Please accept the terms and conditions')
      })
    })
  })

  describe('Navigation and UI', () => {
    it('should navigate between steps correctly', () => {
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Fill step 1
      cy.get('input[placeholder*="starting date"]').type('2024-12-01')
      cy.get('input[placeholder*="finishing date"]').type('2024-12-05')
      cy.get('input[placeholder*="listing title"]').type('Test Tour')
      cy.get('.next-btn-dark').click()
      
      // Verify step 2 is active
      cy.get('.step-item').eq(1).should('have.class', 'active')
      
      // Go back to step 1
      cy.get('button').contains('Previous').click()
      cy.get('.step-item').first().should('have.class', 'active')
      
      // Go forward again
      cy.get('.next-btn-dark').click()
      cy.get('.step-item').eq(1).should('have.class', 'active')
    })

    it('should handle responsive design', () => {
      cy.get('input[value="single-date"]').click()
      cy.get('.listing-next-btn').click()
      
      // Test mobile viewport
      cy.viewport('iphone-6')
      cy.get('.custom-stepper').should('be.visible')
      cy.get('.step-item').should('be.visible')
      
      // Test tablet viewport
      cy.viewport('ipad-2')
      cy.get('.custom-stepper').should('be.visible')
      
      // Test desktop viewport
      cy.viewport(1280, 720)
      cy.get('.custom-stepper').should('be.visible')
    })
  })
}) 
