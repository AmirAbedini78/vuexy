import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://vuexy.test/api'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Individual User API Service
export const individualUserService = {
  async register(formData) {
    try {
      const formDataToSend = new FormData()
      
      // Step 1: Personal Information
      formDataToSend.append('full_name', formData.fullName || '')
      formDataToSend.append('nationality', formData.nationality || '')
      formDataToSend.append('address1', formData.address1 || '')
      formDataToSend.append('city', formData.city || '')
      formDataToSend.append('state', formData.state || '')
      formDataToSend.append('dob', formData.dob || '')
      if (formData.languages && formData.languages.length > 0) {
        formDataToSend.append('languages', JSON.stringify(formData.languages))
      }
      formDataToSend.append('address2', formData.address2 || '')
      formDataToSend.append('postal_code', formData.postalCode || '')
      formDataToSend.append('country', formData.country || '')
      
      // Step 2: Account Details
      if (formData.passportImage && formData.passportImage instanceof File) {
        formDataToSend.append('passport_image', formData.passportImage)
      }
      if (formData.avatarImage && formData.avatarImage instanceof File) {
        formDataToSend.append('avatar_image', formData.avatarImage)
      }
      formDataToSend.append('activity_specialization', formData.activitySpecialization || '')
      formDataToSend.append('years_of_experience', formData.yearsOfExperience || '')
      formDataToSend.append('emergency_contact_name', formData.emergencyContactName || '')
      formDataToSend.append('want_to_be_listed', formData.wantToBeListed || '')
      formDataToSend.append('short_bio', formData.shortBio || '')
      if (formData.certifications && formData.certifications instanceof File) {
        formDataToSend.append('certifications', formData.certifications)
      }
      formDataToSend.append('country_of_operation', formData.countryOfOperation || '')
      formDataToSend.append('emergency_contact_phone', formData.emergencyContactPhone || '')
      
      // Step 3: Social Links
      if (formData.socialMediaLinks && formData.socialMediaLinks.length > 0) {
        // Filter out empty strings
        const filteredLinks = formData.socialMediaLinks.filter(link => link && link.trim() !== '')
        formDataToSend.append('social_media_links', JSON.stringify(filteredLinks))
      } else {
        formDataToSend.append('social_media_links', JSON.stringify([]))
      }
      if (formData.socialProofLinks && formData.socialProofLinks.length > 0) {
        // Filter out empty strings
        const filteredLinks = formData.socialProofLinks.filter(link => link && link.trim() !== '')
        formDataToSend.append('social_proof_links', JSON.stringify(filteredLinks))
      } else {
        formDataToSend.append('social_proof_links', JSON.stringify([]))
      }
      formDataToSend.append('terms_accepted', formData.termsAccepted ? '1' : '0')

      const response = await api.post('/individual-users', formDataToSend, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  },

  async getById(id) {
    try {
      const response = await api.get(`/individual-users/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  },
}



// Company User API Service
export const companyUserService = {
  async register(formData) {
    try {
      const formDataToSend = new FormData()
      
      // Step 1: Company Information
      formDataToSend.append('company_name', formData.companyName || '')
      formDataToSend.append('vat_id', formData.vatId || '')
      formDataToSend.append('address1', formData.address1 || '')
      formDataToSend.append('city', formData.city || '')
      formDataToSend.append('state', formData.state || '')
      formDataToSend.append('contact_person', formData.contactPerson || '')
      formDataToSend.append('country_of_registration', formData.countryOfRegistration || '')
      formDataToSend.append('address2', formData.address2 || '')
      formDataToSend.append('postal_code', formData.postalCode || '')
      formDataToSend.append('country', formData.country || '')
      formDataToSend.append('business_type', formData.businessType || '')
      
      // Step 2: Business Details
      if (formData.passportImage && formData.passportImage instanceof File) {
        formDataToSend.append('passport_image', formData.passportImage)
      }
      if (formData.avatarImage && formData.avatarImage instanceof File) {
        formDataToSend.append('avatar_image', formData.avatarImage)
      }
      formDataToSend.append('activity_specialization', formData.activitySpecialization || '')
      formDataToSend.append('want_to_be_listed', formData.wantToBeListed || '')
      formDataToSend.append('short_bio', formData.shortBio || '')
      if (formData.certifications && formData.certifications instanceof File) {
        formDataToSend.append('certifications', formData.certifications)
      }
      
      // Step 3: Social Links
      formDataToSend.append('company_website', formData.companyWebsite || '')
      if (formData.socialMediaLinks && formData.socialMediaLinks.length > 0) {
        // Filter out empty strings
        const filteredLinks = formData.socialMediaLinks.filter(link => link && link.trim() !== '')
        formDataToSend.append('social_media_links', JSON.stringify(filteredLinks))
      } else {
        formDataToSend.append('social_media_links', JSON.stringify([]))
      }
      if (formData.socialProofLinks && formData.socialProofLinks.length > 0) {
        // Filter out empty strings
        const filteredLinks = formData.socialProofLinks.filter(link => link && link.trim() !== '')
        formDataToSend.append('social_proof_links', JSON.stringify(filteredLinks))
      } else {
        formDataToSend.append('social_proof_links', JSON.stringify([]))
      }
      formDataToSend.append('terms_accepted', formData.termsAccepted ? '1' : '0')

      const response = await api.post('/company-users', formDataToSend, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  },

  async getById(id) {
    try {
      const response = await api.get(`/company-users/${id}`)
      return response.data
    } catch (error) {
      throw error.response?.data || error.message
    }
  },
}

export default api 
