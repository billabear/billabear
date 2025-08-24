import { ref, reactive } from 'vue'
import axios from 'axios'

/**
 * Composable for handling API requests with loading states and error management
 * Replaces common patterns found in Options API components
 */
export function useApi() {
  const loading = ref(false)
  const error = ref(null)
  const errors = reactive({})

  /**
   * Make a GET request
   * @param {string} url - The API endpoint
   * @returns {Promise} - The axios response
   */
  const get = async (url) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(url)
      loading.value = false
      return response
    } catch (err) {
      handleError(err)
      throw err
    }
  }

  /**
   * Make a POST request
   * @param {string} url - The API endpoint
   * @param {Object} data - The request payload
   * @returns {Promise} - The axios response
   */
  const post = async (url, data = {}) => {
    loading.value = true
    error.value = null
    Object.keys(errors).forEach(key => delete errors[key])
    
    try {
      const response = await axios.post(url, data)
      loading.value = false
      return response
    } catch (err) {
      handleError(err)
      throw err
    }
  }

  /**
   * Make a PUT request
   * @param {string} url - The API endpoint
   * @param {Object} data - The request payload
   * @returns {Promise} - The axios response
   */
  const put = async (url, data = {}) => {
    loading.value = true
    error.value = null
    Object.keys(errors).forEach(key => delete errors[key])
    
    try {
      const response = await axios.put(url, data)
      loading.value = false
      return response
    } catch (err) {
      handleError(err)
      throw err
    }
  }

  /**
   * Make a DELETE request
   * @param {string} url - The API endpoint
   * @returns {Promise} - The axios response
   */
  const del = async (url) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.delete(url)
      loading.value = false
      return response
    } catch (err) {
      handleError(err)
      throw err
    }
  }

  /**
   * Handle API errors consistently
   * @param {Error} err - The error object from axios
   */
  const handleError = (err) => {
    loading.value = false
    
    if (err.response && err.response.data) {
      // Handle validation errors
      if (err.response.data.errors) {
        Object.assign(errors, err.response.data.errors)
      }
      
      // Handle general error messages
      if (err.response.data.message) {
        error.value = err.response.data.message
      }
    } else {
      error.value = 'An unexpected error occurred'
    }
  }

  /**
   * Reset error states
   */
  const resetErrors = () => {
    error.value = null
    Object.keys(errors).forEach(key => delete errors[key])
  }

  return {
    loading,
    error,
    errors,
    get,
    post,
    put,
    delete: del,
    resetErrors
  }
}