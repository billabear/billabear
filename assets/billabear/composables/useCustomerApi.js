import { ref, reactive } from 'vue'
import axios from 'axios'

export function useCustomerApi() {
  const loading = ref(false)
  const loaded = ref(false)
  const hasError = ref(false)
  const customers = ref([])
  const hasMore = ref(false)
  const showBack = ref(false)
  const lastKey = ref(null)
  const firstKey = ref(null)
  const previousLastKey = ref(null)
  const nextPageInProgress = ref(false)
  const perPage = ref("10")

  const filters = reactive({
    email: {
      label: 'app.customer.list.filter.email',
      type: 'text',
      value: null
    },
    reference: {
      label: 'app.customer.list.filter.reference',
      type: 'text',
      value: null
    },
    country: {
      label: 'app.customer.list.filter.country',
      type: 'text',
      value: null
    },
    external_reference: {
      label: 'app.customer.list.filter.external_reference',
      type: 'text',
      value: null
    }
  })

  const activeFilters = ref([])

  const buildQuery = () => {
    const params = new URLSearchParams()
    
    if (lastKey.value) {
      params.append('last_key', lastKey.value)
    }
    
    params.append('limit', perPage.value)

    // Add active filters to query
    Object.keys(filters).forEach(key => {
      if (filters[key].value && filters[key].value.trim() !== '') {
        params.append(key, filters[key].value)
      }
    })

    return params.toString()
  }

  const fetchCustomers = async (direction = 'forward') => {
    loading.value = true
    loaded.value = false
    
    try {
      const query = buildQuery()
      const response = await axios.get(`/app/customer?${query}`)
      
      customers.value = response.data.data || []
      hasMore.value = response.data.has_more || false
      
      if (direction === 'forward') {
        previousLastKey.value = lastKey.value
        lastKey.value = response.data.last_key || null
        showBack.value = !!previousLastKey.value
      } else if (direction === 'backward') {
        lastKey.value = previousLastKey.value
        previousLastKey.value = null
        showBack.value = false
      }

      firstKey.value = response.data.first_key || null
      loaded.value = true
      hasError.value = false
    } catch (error) {
      console.error('Error fetching customers:', error)
      hasError.value = true
      customers.value = []
    } finally {
      loading.value = false
      nextPageInProgress.value = false
    }
  }

  const nextPage = async () => {
    if (nextPageInProgress.value || !hasMore.value) return
    nextPageInProgress.value = true
    await fetchCustomers('forward')
  }

  const prevPage = async () => {
    if (nextPageInProgress.value || !showBack.value) return
    nextPageInProgress.value = true
    await fetchCustomers('backward')
  }

  const changePerPage = async (newPerPage) => {
    perPage.value = newPerPage
    lastKey.value = null
    previousLastKey.value = null
    showBack.value = false
    await fetchCustomers()
  }

  const applyFilters = async () => {
    lastKey.value = null
    previousLastKey.value = null
    showBack.value = false
    await fetchCustomers()
  }

  const clearFilters = async () => {
    Object.keys(filters).forEach(key => {
      filters[key].value = null
    })
    activeFilters.value = []
    await applyFilters()
  }

  return {
    // State
    loading,
    loaded,
    hasError,
    customers,
    hasMore,
    showBack,
    perPage,
    filters,
    activeFilters,
    nextPageInProgress,

    // Methods
    fetchCustomers,
    nextPage,
    prevPage,
    changePerPage,
    applyFilters,
    clearFilters
  }
}