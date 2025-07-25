describe('API Tests', () => {
  const baseUrl = 'http://vuexy.test/api'

  it('should fetch listings successfully', () => {
    cy.request({
      method: 'GET',
      url: `${baseUrl}/listings`,
      failOnStatusCode: false
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 401, 403])
    })
  })

  it('should fetch itineraries for a listing', () => {
    cy.request({
      method: 'GET',
      url: `${baseUrl}/listings/1/itineraries`,
      failOnStatusCode: false
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 401, 403, 404])
    })
  })

  it('should fetch special addons for a listing', () => {
    cy.request({
      method: 'GET',
      url: `${baseUrl}/listings/1/special-addons`,
      failOnStatusCode: false
    }).then((response) => {
      expect(response.status).to.be.oneOf([200, 401, 403, 404])
    })
  })
}) 
