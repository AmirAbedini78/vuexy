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
    includeFields = null
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

  // Save data to localStorage
  const saveToStorage = () => {
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

      localStorage.setItem(storageKey, JSON.stringify(dataWithMeta))
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
