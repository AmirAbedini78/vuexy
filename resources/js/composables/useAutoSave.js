import { $api } from '@/utils/api'
import { onMounted, onUnmounted, ref, watch } from 'vue'

/**
 * Auto-save composable for form data
 * @param {Object} formData - Reactive form data object
 * @param {String} storageKey - localStorage key for saving data
 * @param {Number} debounceMs - Debounce delay in milliseconds (default: 1000)
 * @param {Function} onSave - Optional callback when data is saved
 * @param {Function} onLoad - Optional callback when data is loaded
 */
export function useAutoSave(formData, storageKey, options = {}) {
  const {
    debounceMs = 500, // کاهش تاخیر
    onSave = null,
    onLoad = null,
    excludeFields = [],
    includeFields = null,
    saveToDatabase = false,
    listingType = 'single-date',
    apiEndpoint = '/auto-save-listings'
  } = options

  const isSaving = ref(false)
  const lastSaved = ref(null)
  const hasUnsavedChanges = ref(false)
  const saveTimeout = ref(null)
  const showSavedIndicator = ref(false)
  const hideTimeout = ref(null)

  // Debounced save function
  const debouncedSave = () => {
    if (saveTimeout.value) {
      clearTimeout(saveTimeout.value)
    }
    
    saveTimeout.value = setTimeout(() => {
      saveToStorage()
    }, debounceMs)
  }

  // Save data to localStorage and optionally to database
  const saveToStorage = async () => {
    try {
      isSaving.value = true
      
      // Filter data based on include/exclude fields
      let dataToSave = { ...formData.value }
      
      if (includeFields && Array.isArray(includeFields)) {
        dataToSave = includeFields.reduce((acc, field) => {
          if (formData.value.hasOwnProperty(field)) {
            acc[field] = formData.value[field]
          }
          return acc
        }, {})
      } else if (excludeFields && Array.isArray(excludeFields)) {
        excludeFields.forEach(field => {
          delete dataToSave[field]
        })
      }

      // Add metadata
      const dataWithMeta = {
        ...dataToSave,
        _autoSave: {
          timestamp: new Date().toISOString(),
          version: '1.0'
        }
      }

      // Save to localStorage
      localStorage.setItem(storageKey, JSON.stringify(dataWithMeta))
      
      // Save to database if enabled
      if (saveToDatabase) {
        try {
          const apiData = {
            listing_type: listingType,
            form_data: dataToSave,
            adventure_title: dataToSave.listingTitle || dataToSave.adventure_title || '',
            description: dataToSave.description || dataToSave.listingDescription || '',
            location: dataToSave.location || dataToSave.locations || '',
            price: dataToSave.price || 0,
            min_capacity: dataToSave.min_capacity || dataToSave.minCapacity || null,
            max_capacity: dataToSave.max_capacity || dataToSave.maxCapacity || null,
            group_language: dataToSave.groupLanguage || '',
            experience_level: dataToSave.experienceLevel || '',
            fitness_level: dataToSave.fitnessLevel || '',
            activities_included: dataToSave.activitiesIncluded || '',
            maps_and_routes: dataToSave.mapsAndRoutes || [],
            listing_media: dataToSave.listingMedia || [],
            promotional_video: dataToSave.promotionalVideo || [],
            whats_included: dataToSave.whatsIncluded || '',
            whats_not_included: dataToSave.whatsNotIncluded || '',
            additional_notes: dataToSave.additionalNotes || '',
            providers_faq: dataToSave.providersFAQ || '',
            personal_policies: dataToSave.personalPolicies || '',
            starting_date: dataToSave.startingDate || '',
            finishing_date: dataToSave.finishingDate || '',
            min_advance_notice: dataToSave.minAdvanceNotice || '',
            max_advance_booking: dataToSave.maxAdvanceBooking || '',
            available_days: dataToSave.availableDays || [],
            packages: dataToSave.packages || [],
            special_addons: dataToSave.specialAddons || [],
            itinerary: dataToSave.itinerary || [],
            periods: dataToSave.periods || []
          }

          const response = await $api(apiEndpoint, {
            method: 'POST',
            body: apiData
          })
          
          // Store auto-save ID in form data
          if (response && response.data && response.data.id) {
            formData.value.autoSaveId = response.data.id
          }
          
          console.log('Auto-save data saved to database')
        } catch (dbError) {
          console.error('Error saving to database:', dbError)
          // Continue with localStorage save even if database save fails
        }
      }
      
      lastSaved.value = new Date()
      hasUnsavedChanges.value = false
      
      // Show saved indicator and hide after 2 seconds
      showSavedIndicator.value = true
      if (hideTimeout.value) {
        clearTimeout(hideTimeout.value)
      }
      hideTimeout.value = setTimeout(() => {
        showSavedIndicator.value = false
      }, 2000)
      
      if (onSave) {
        onSave(dataWithMeta)
      }
      
      console.log(`Auto-saved form data to ${storageKey}`)
    } catch (error) {
      console.error('Error saving form data:', error)
    } finally {
      isSaving.value = false
    }
  }

  // Load data from localStorage
  const loadFromStorage = () => {
    try {
      const savedData = localStorage.getItem(storageKey)
      if (savedData) {
        const parsedData = JSON.parse(savedData)
        
        // Remove metadata before applying to form
        const { _autoSave, ...formDataToLoad } = parsedData
        
        // Apply loaded data to form
        Object.keys(formDataToLoad).forEach(key => {
          if (formData.value.hasOwnProperty(key)) {
            formData.value[key] = formDataToLoad[key]
          }
        })
        
        hasUnsavedChanges.value = false
        
        if (onLoad) {
          onLoad(formDataToLoad, _autoSave)
        }
        
        console.log(`Loaded form data from ${storageKey}`)
        return true
      }
    } catch (error) {
      console.error('Error loading form data:', error)
    }
    return false
  }

  // Clear saved data
  const clearSavedData = () => {
    try {
      localStorage.removeItem(storageKey)
      hasUnsavedChanges.value = false
      lastSaved.value = null
      console.log(`Cleared saved data for ${storageKey}`)
    } catch (error) {
      console.error('Error clearing saved data:', error)
    }
  }

  // Watch for changes in form data
  const stopWatching = watch(
    formData,
    (newData, oldData) => {
      // Skip if this is the initial load
      if (oldData === undefined) return
      
      hasUnsavedChanges.value = true
      debouncedSave()
    },
    { deep: true }
  )

  // Load data on mount
  onMounted(() => {
    loadFromStorage()
  })

  // Cleanup on unmount
  onUnmounted(() => {
    if (saveTimeout.value) {
      clearTimeout(saveTimeout.value)
    }
    if (hideTimeout.value) {
      clearTimeout(hideTimeout.value)
    }
    stopWatching()
  })

  // Manual save
  const forceSave = () => {
    if (saveTimeout.value) {
      clearTimeout(saveTimeout.value)
    }
    saveToStorage()
  }

  // Check if there's saved data
  const hasSavedData = () => {
    return localStorage.getItem(storageKey) !== null
  }

  // Get saved data info
  const getSavedDataInfo = () => {
    try {
      const savedData = localStorage.getItem(storageKey)
      if (savedData) {
        const parsedData = JSON.parse(savedData)
        return parsedData._autoSave || null
      }
    } catch (error) {
      console.error('Error getting saved data info:', error)
    }
    return null
  }

  return {
    isSaving,
    lastSaved,
    hasUnsavedChanges,
    showSavedIndicator,
    saveToStorage: forceSave,
    loadFromStorage,
    clearSavedData,
    hasSavedData,
    getSavedDataInfo
  }
}
