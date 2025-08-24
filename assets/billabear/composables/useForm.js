import { ref, reactive, computed } from 'vue'
import { useApi } from './useApi'

/**
 * Composable for handling form state, validation, and submission
 * Replaces common form patterns found in Options API components
 */
export function useForm(initialData = {}) {
  const { post, put, loading, error, errors, resetErrors } = useApi()
  
  // Form data - reactive object that mirrors the original data structure
  const formData = reactive({ ...initialData })
  
  // Form states
  const isSubmitting = computed(() => loading.value)
  const success = ref(false)
  const failed = ref(false)

  /**
   * Reset form to initial state
   */
  const resetForm = () => {
    Object.keys(formData).forEach(key => {
      if (typeof initialData[key] === 'object' && initialData[key] !== null) {
        Object.assign(formData[key], initialData[key])
      } else {
        formData[key] = initialData[key]
      }
    })
    resetErrors()
    success.value = false
    failed.value = false
  }

  /**
   * Update form data
   * @param {Object} data - New data to merge into form
   */
  const updateFormData = (data) => {
    Object.assign(formData, data)
  }

  /**
   * Set form field value
   * @param {string} field - Field name (supports dot notation for nested fields)
   * @param {*} value - Value to set
   */
  const setField = (field, value) => {
    const keys = field.split('.')
    let target = formData
    
    for (let i = 0; i < keys.length - 1; i++) {
      if (!(keys[i] in target) || typeof target[keys[i]] !== 'object') {
        target[keys[i]] = {}
      }
      target = target[keys[i]]
    }
    
    target[keys[keys.length - 1]] = value
  }

  /**
   * Get form field value
   * @param {string} field - Field name (supports dot notation for nested fields)
   * @returns {*} - Field value
   */
  const getField = (field) => {
    const keys = field.split('.')
    let value = formData
    
    for (const key of keys) {
      if (value && typeof value === 'object' && key in value) {
        value = value[key]
      } else {
        return undefined
      }
    }
    
    return value
  }

  /**
   * Check if field has validation error
   * @param {string} field - Field name
   * @returns {boolean} - True if field has error
   */
  const hasError = (field) => {
    return field in errors
  }

  /**
   * Get field error message
   * @param {string} field - Field name
   * @returns {string|null} - Error message or null
   */
  const getError = (field) => {
    return errors[field] || null
  }

  /**
   * Submit form via POST request
   * @param {string} url - API endpoint
   * @param {Object} options - Additional options
   * @returns {Promise} - The response promise
   */
  const submitForm = async (url, options = {}) => {
    const { 
      transformData = (data) => data,
      onSuccess = () => {},
      onError = () => {}
    } = options

    success.value = false
    failed.value = false
    
    try {
      // Clean empty strings to null if specified
      const cleanedData = cleanFormData(formData, options.cleanEmpty)
      const payload = transformData(cleanedData)
      
      const response = await post(url, payload)
      success.value = true
      failed.value = false
      onSuccess(response)
      
      return response
    } catch (err) {
      success.value = false
      failed.value = true
      onError(err)
      throw err
    }
  }

  /**
   * Update form via PUT request
   * @param {string} url - API endpoint
   * @param {Object} options - Additional options
   * @returns {Promise} - The response promise
   */
  const updateForm = async (url, options = {}) => {
    const { 
      transformData = (data) => data,
      onSuccess = () => {},
      onError = () => {}
    } = options

    success.value = false
    failed.value = false
    
    try {
      const cleanedData = cleanFormData(formData, options.cleanEmpty)
      const payload = transformData(cleanedData)
      
      const response = await put(url, payload)
      success.value = true
      failed.value = false
      onSuccess(response)
      
      return response
    } catch (err) {
      success.value = false
      failed.value = true
      onError(err)
      throw err
    }
  }

  /**
   * Clean form data by converting empty strings to null
   * @param {Object} data - Form data to clean
   * @param {boolean} shouldClean - Whether to perform cleaning
   * @returns {Object} - Cleaned data
   */
  const cleanFormData = (data, shouldClean = true) => {
    if (!shouldClean) return { ...data }
    
    const cleaned = {}
    
    for (const [key, value] of Object.entries(data)) {
      if (typeof value === 'string' && value === '') {
        cleaned[key] = null
      } else if (typeof value === 'object' && value !== null) {
        cleaned[key] = cleanFormData(value, shouldClean)
      } else {
        cleaned[key] = value
      }
    }
    
    return cleaned
  }

  return {
    formData,
    isSubmitting,
    success,
    failed,
    error,
    errors,
    resetForm,
    updateFormData,
    setField,
    getField,
    hasError,
    getError,
    submitForm,
    updateForm,
    resetErrors
  }
}