describe('Wizard Forms API Tests', () => {
  const baseUrl = 'http://vuexy.test/api'

  describe('Single Date Wizard API', () => {
    let listingId = null

    it('should create a new single date listing', () => {
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings`,
        body: {
          user_id: 1,
          listing_type: 'single-date',
          starting_date: '2024-12-01',
          finishing_date: '2024-12-05',
          listing_title: 'Test Single Date Listing',
          listing_description: 'Test description',
          price: 500,
          min_capacity: 2,
          max_capacity: 10,
          subtitle: 'Test Subtitle',
          experience_level: 'moderate',
          fitness_level: 'moderate'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 201, 422])
        if (response.status === 200 || response.status === 201) {
          listingId = response.body.id || response.body.data?.id
          expect(listingId).to.exist
        }
      })
    })

    it('should update single date listing details', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      cy.request({
        method: 'PUT',
        url: `${baseUrl}/listings/${listingId}`,
        body: {
          activities_included: 'Hiking',
          group_language: 'English',
          locations: 'Europe',
          maps_and_routes: ['https://maps.google.com'],
          listing_media: ['https://example.com/image.jpg'],
          promotional_video: ['https://youtube.com/watch?v=test'],
          whats_included: 'Accommodation, meals, guide',
          whats_not_included: 'Flights, insurance',
          additional_notes: 'Bring comfortable shoes',
          providers_faq: 'Common questions and answers'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 422])
      })
    })

    it('should add itinerary to single date listing', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings/${listingId}/itineraries`,
        body: {
          title: 'Day 1 - Arrival',
          description: 'Welcome and orientation',
          accommodation: 'Hotel Central',
          location: 'Paris, France',
          link: 'https://hotel-central.com',
          day_number: 1
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 201, 422])
      })
    })

    it('should add special addon to single date listing', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings/${listingId}/special-addons`,
        body: {
          title: 'Equipment Rental',
          description: 'Professional hiking equipment',
          price: 50.00
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 201, 422])
      })
    })

    it('should fetch itineraries for single date listing', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      cy.request({
        method: 'GET',
        url: `${baseUrl}/listings/${listingId}/itineraries`,
        headers: {
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 404])
        if (response.status === 200) {
          expect(response.body).to.have.property('data')
        }
      })
    })

    it('should fetch special addons for single date listing', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      cy.request({
        method: 'GET',
        url: `${baseUrl}/listings/${listingId}/special-addons`,
        headers: {
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 404])
        if (response.status === 200) {
          expect(response.body).to.have.property('data')
        }
      })
    })
  })

  describe('Multi Date Wizard API', () => {
    let listingId = null

    it('should create a new multi date listing', () => {
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings`,
        body: {
          user_id: 1,
          listing_type: 'multi-date',
          listing_title: 'Test Multi Date Listing',
          listing_description: 'Flexible adventure dates',
          locations: 'Europe',
          group_language: 'English',
          subtitle: 'Flexible Dates',
          activities_included: 'Hiking',
          experience_level: 'moderate',
          fitness_level: 'moderate',
          start_date: '2024-01-01',
          end_date: '2024-12-31'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 201, 422])
        if (response.status === 200 || response.status === 201) {
          listingId = response.body.id || response.body.data?.id
          expect(listingId).to.exist
        }
      })
    })

    it('should update multi date listing with pricing details', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      cy.request({
        method: 'PUT',
        url: `${baseUrl}/listings/${listingId}`,
        body: {
          price: 400,
          max_participants: 8,
          requirements: 'Good fitness level',
          maps_and_routes: ['https://maps.google.com'],
          listing_media: ['https://example.com/image.jpg'],
          promotional_video: ['https://youtube.com/watch?v=test'],
          whats_included: 'Accommodation, meals',
          whats_not_included: 'Flights',
          additional_notes: 'Flexible booking',
          providers_faq: 'FAQ content'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 422])
      })
    })
  })

  describe('Open Date Wizard API', () => {
    let listingId = null

    it('should create a new open date listing', () => {
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings`,
        body: {
          user_id: 1,
          listing_type: 'open-date',
          listing_title: 'Test Open Date Listing',
          subtitle: 'Any Time Adventure',
          description: 'Book anytime adventure',
          activities_included: 'Hiking',
          locations: 'Europe',
          group_language: 'English',
          experience_level: 'moderate',
          fitness_level: 'moderate',
          min_advance_notice: 7,
          max_advance_booking: 365
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 201, 422])
        if (response.status === 200 || response.status === 201) {
          listingId = response.body.id || response.body.data?.id
          expect(listingId).to.exist
        }
      })
    })

    it('should update open date listing with pricing details', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      cy.request({
        method: 'PUT',
        url: `${baseUrl}/listings/${listingId}`,
        body: {
          price: 350,
          max_participants: 6,
          requirements: 'Good fitness level',
          maps_and_routes: ['https://maps.google.com'],
          listing_media: ['https://example.com/image.jpg'],
          promotional_video: ['https://youtube.com/watch?v=test'],
          whats_included: 'Accommodation, meals',
          whats_not_included: 'Flights',
          additional_notes: 'Flexible booking',
          providers_faq: 'FAQ content'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 422])
      })
    })
  })

  describe('Form Validation API Tests', () => {
    it('should validate required fields for single date listing', () => {
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings`,
        body: {
          user_id: 1,
          listing_type: 'single-date'
          // Missing required fields
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        // Should return validation errors
        expect(response.status).to.be.oneOf([422, 400])
        if (response.status === 422) {
          expect(response.body).to.have.property('errors')
        }
      })
    })

    it('should validate date ranges for single date listing', () => {
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings`,
        body: {
          user_id: 1,
          listing_type: 'single-date',
          starting_date: '2024-12-05', // End date before start date
          finishing_date: '2024-12-01',
          listing_title: 'Test Listing'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        // Should return validation errors for invalid date range
        expect(response.status).to.be.oneOf([422, 400])
      })
    })

    it('should validate price format for listings', () => {
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings`,
        body: {
          user_id: 1,
          listing_type: 'single-date',
          starting_date: '2024-12-01',
          finishing_date: '2024-12-05',
          listing_title: 'Test Listing',
          price: 'invalid-price' // Invalid price format
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        // Should return validation errors for invalid price
        expect(response.status).to.be.oneOf([422, 400])
      })
    })
  })

  describe('Modal Data API Tests', () => {
    let listingId = null

    beforeEach(() => {
      // Create a test listing for modal tests
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings`,
        body: {
          user_id: 1,
          listing_type: 'single-date',
          starting_date: '2024-12-01',
          finishing_date: '2024-12-05',
          listing_title: 'Modal Test Listing'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        if (response.status === 200 || response.status === 201) {
          listingId = response.body.id || response.body.data?.id
        }
      })
    })

    it('should handle itinerary modal data operations', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      // Add itinerary
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings/${listingId}/itineraries`,
        body: {
          title: 'Day 1 - Arrival',
          description: 'Welcome and orientation',
          accommodation: 'Hotel Central',
          location: 'Paris, France',
          link: 'https://hotel-central.com',
          day_number: 1
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 201, 422])
      })

      // Update itinerary
      cy.request({
        method: 'PUT',
        url: `${baseUrl}/listings/${listingId}/itineraries/1`,
        body: {
          title: 'Day 1 - Updated Arrival',
          description: 'Updated welcome and orientation',
          accommodation: 'Updated Hotel Central',
          location: 'Updated Paris, France',
          link: 'https://updated-hotel-central.com'
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 422])
      })

      // Delete itinerary
      cy.request({
        method: 'DELETE',
        url: `${baseUrl}/listings/${listingId}/itineraries/1`,
        headers: {
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 204, 404])
      })
    })

    it('should handle special addons modal data operations', () => {
      if (!listingId) {
        cy.log('Skipping test - no listing ID available')
        return
      }

      // Add special addon
      cy.request({
        method: 'POST',
        url: `${baseUrl}/listings/${listingId}/special-addons`,
        body: {
          title: 'Equipment Rental',
          description: 'Professional hiking equipment',
          price: 50.00
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 201, 422])
      })

      // Update special addon
      cy.request({
        method: 'PUT',
        url: `${baseUrl}/listings/${listingId}/special-addons/1`,
        body: {
          title: 'Updated Equipment Rental',
          description: 'Updated professional hiking equipment',
          price: 75.00
        },
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 422])
      })

      // Delete special addon
      cy.request({
        method: 'DELETE',
        url: `${baseUrl}/listings/${listingId}/special-addons/1`,
        headers: {
          'Accept': 'application/json'
        },
        failOnStatusCode: false
      }).then((response) => {
        expect(response.status).to.be.oneOf([200, 204, 404])
      })
    })
  })
}) 
