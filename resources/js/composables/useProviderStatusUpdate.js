import { useProviderStatus } from '@/composables/useProviderStatus'

export const useProviderStatusUpdate = () => {
  const { fetchProviderStatus } = useProviderStatus()

  // Function to refresh provider status after admin changes
  const refreshProviderStatus = async () => {
    try {
      await fetchProviderStatus()
      console.log('Provider status refreshed after admin update')
    } catch (error) {
      console.error('Error refreshing provider status:', error)
    }
  }

  // Function to update provider status in localStorage (for immediate UI update)
  const updateProviderStatusInStorage = (status, providerType = null, providerId = null) => {
    localStorage.setItem('providerStatus', status)
    if (providerType) {
      localStorage.setItem('providerType', providerType)
    }
    if (providerId) {
      localStorage.setItem('providerId', providerId)
    }
    
    console.log('Provider status updated in storage:', {
      status,
      providerType,
      providerId
    })
  }

  return {
    refreshProviderStatus,
    updateProviderStatusInStorage
  }
}
