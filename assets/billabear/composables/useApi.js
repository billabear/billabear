import { ref } from 'vue'
import axios from 'axios'

export function useApi() {
  const loading = ref(false)
  const error = ref(null)
  const data = ref(null)

  const clearError = () => {
    error.value = null
  }

  const clearData = () => {
    data.value = null
  }

  const handleRequest = async (requestPromise) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await requestPromise
      data.value = response.data
      return response
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  const get = async (url, config = {}) => {
    return handleRequest(axios.get(url, config))
  }

  const post = async (url, payload = {}, config = {}) => {
    return handleRequest(axios.post(url, payload, config))
  }

  const put = async (url, payload = {}, config = {}) => {
    return handleRequest(axios.put(url, payload, config))
  }

  const patch = async (url, payload = {}, config = {}) => {
    return handleRequest(axios.patch(url, payload, config))
  }

  const del = async (url, config = {}) => {
    return handleRequest(axios.delete(url, config))
  }

  const request = async (config) => {
    return handleRequest(axios(config))
  }

  return {
    // State
    loading,
    error,
    data,

    // Methods
    get,
    post,
    put,
    patch,
    del,
    request,
    clearError,
    clearData
  }
}