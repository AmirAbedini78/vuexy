import { computed, ref } from 'vue'

// Global provider status state
const providerStatus = ref(null)
const providerType = ref(null)
const providerId = ref(null)
const loading = ref(false)
const error = ref(null)

export const useProviderStatus = () => {
  // Fetch provider status from API
  const fetchProviderStatus = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await $api('/provider/status')
      
      if (response.success) {
        providerStatus.value = response.status
        providerType.value = response.provider_type
        providerId.value = response.provider_id
        
        // Store in localStorage for persistence
        localStorage.setItem('providerStatus', response.status)
        localStorage.setItem('providerType', response.provider_type || '')
        localStorage.setItem('providerId', response.provider_id || '')
        
        console.log('Provider status fetched:', {
          status: response.status,
          type: response.provider_type,
          id: response.provider_id
        })
      } else {
        // No provider profile found
        providerStatus.value = 'not_found'
        providerType.value = null
        providerId.value = null
        
        // Clear localStorage
        localStorage.removeItem('providerStatus')
        localStorage.removeItem('providerType')
        localStorage.removeItem('providerId')
        
        console.log('No provider profile found')
      }
    } catch (err) {
      console.error('Error fetching provider status:', err)
      error.value = err.message || 'Failed to fetch provider status'
      
      // Try to get from localStorage as fallback
      const storedStatus = localStorage.getItem('providerStatus')
      if (storedStatus) {
        providerStatus.value = storedStatus
        providerType.value = localStorage.getItem('providerType')
        providerId.value = localStorage.getItem('providerId')
      }
    } finally {
      loading.value = false
    }
  }

  // Initialize from localStorage on first load
  const initializeFromStorage = () => {
    const storedStatus = localStorage.getItem('providerStatus')
    const storedType = localStorage.getItem('providerType')
    const storedId = localStorage.getItem('providerId')
    
    if (storedStatus) {
      providerStatus.value = storedStatus
      providerType.value = storedType
      providerId.value = storedId
      
      console.log('Provider status initialized from storage:', {
        status: storedStatus,
        type: storedType,
        id: storedId
      })
    }
  }

  // Computed properties
  const isActive = computed(() => providerStatus.value === 'active')
  const isApproved = computed(() => providerStatus.value === 'approved')
  const isRejected = computed(() => providerStatus.value === 'rejected')
  const isNotFound = computed(() => providerStatus.value === 'not_found')
  const hasProviderProfile = computed(() => providerStatus.value && providerStatus.value !== 'not_found')
  
  // Check if user should see full sidebar (only if status is 'active')
  const shouldShowFullSidebar = computed(() => isActive.value)
  
  // Check if user should see limited sidebar (only Welcome menu)
  const shouldShowLimitedSidebar = computed(() => !isActive.value && hasProviderProfile.value)

  return {
    // State
    providerStatus,
    providerType,
    providerId,
    loading,
    error,
    
    // Computed
    isActive,
    isApproved,
    isRejected,
    isNotFound,
    hasProviderProfile,
    shouldShowFullSidebar,
    shouldShowLimitedSidebar,
    
    // Methods
    fetchProviderStatus,
    initializeFromStorage
  }
}
