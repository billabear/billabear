<template>
  <div v-if="!has_error">
    <div class="grid grid-cols-2">
      <div>
        <h1 class="page-title">{{ $t('app.metric.list.title') }}</h1>
      </div>
      <div class="text-end pt-5">
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <router-link :to="{name: 'app.metric.create'}" class="list-btn">{{ $t('app.metric.list.create') }}</router-link>
        </RoleOnlyView>
      </div>
    </div>

    <LoadingScreen :ready="ready">
      <div class="flex">
        <FiltersSection :filters="filters"/>
        <div class="pl-5 flex-1">

          <div class="rounded-lg bg-white shadow p-3">
            <table class="w-full">
              <thead>
                <tr class="border-b border-black">
                  <th class="text-left pb-2">{{ $t('app.metric.list.name') }}</th>
                  <th></th>
                </tr>
              </thead>
              <tbody v-if="loaded">
                <tr v-for="metric in metrics" class="mt-5">
                  <td class="py-3">{{ metric.name }}</td>
                  <td class="py-3"><router-link :to="{name: 'app.metric.view', params: {id: metric.id}}" class="list-btn">{{ $t('app.metric.list.view_btn') }}</router-link></td>
                </tr>
                <tr v-if="metrics.length === 0">
                  <td colspan="5" class="text-center">{{ $t('app.metric.list.no_metrics') }}</td>
                </tr>
              </tbody>
              <tbody v-else>
              <tr v-for="metric in metrics" >
                <td colspan="5" class="py-3 text-center">
                  <LoadingMessage>{{ $t('app.metric.list.loading') }}</LoadingMessage>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="sm:grid sm:grid-cols-2">

            <div class="mt-4">
              <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.metric.list.prev') }}</button>
              <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.metric.list.next') }}</button>
            </div>
            <div class="mt-4 text-end">
              <select class="rounded-lg border border-gray-300"  @change="changePerPage" v-model="per_page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </LoadingScreen>
  </div>
  <div v-else class="error-page">
    {{ $t('app.invoices.list.error_message') }}
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import currency from "currency.js"
import { Dropdown, ListGroup, ListGroupItem } from "flowbite-vue"
import { useApi } from '../../composables/useApi'
import FiltersSection from "../../../components/app/Ui/Section/FiltersSection.vue"
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue"

// Router and route
const route = useRoute()
const router = useRouter()

// API composable
const { get } = useApi()

// Component state
const ready = ref(false)
const loaded = ref(false)
const has_error = ref(false)
const metrics = ref([])
const has_more = ref(false)
const last_key = ref(null)
const first_key = ref(null)
const previous_last_key = ref(null)
const next_page_in_progress = ref(false)
const show_back = ref(false)
const show_filter_menu = ref(false)
const active_filters = ref([])
const per_page = ref("10")

// Filters configuration
const filters = reactive({
  name: {
    label: 'app.metric.list.filter.name',
    type: 'text',
    value: null
  }
})

// Currency formatting function
const currencyFormat = (value) => {
  return currency(value, { fromCents: true })
}

// Sync route query parameters to filters
const syncQueryToFilters = () => {
  Object.keys(filters).forEach(key => {
    if (route.query[key] !== undefined) {
      filters[key].value = route.query[key]
      if (!isActive(key)) {
        active_filters.value.push(key)
      }
    } else {
      filters[key].value = null
      const index = active_filters.value.indexOf(key)
      if (index !== -1) {
        active_filters.value.splice(index, 1)
      }
    }
  })
}

// Build filter query object
const buildFilterQuery = () => {
  const queryVals = {}
  
  active_filters.value.forEach(filter => {
    if (filters[filter].value !== null && filters[filter].value !== undefined) {
      queryVals[filter] = filters[filter].value
    }
  })

  if (route.query.per_page !== undefined) {
    queryVals.per_page = route.query.per_page
    per_page.value = route.query.per_page
  }

  return queryVals
}

// Search functionality
const doSearch = () => {
  const queryVals = buildFilterQuery()
  router.push({ query: queryVals })
}

// Pagination functions
const nextPage = () => {
  const queryVals = buildFilterQuery()
  queryVals.last_key = last_key.value
  router.push({ query: queryVals })
}

const prevPage = () => {
  const queryVals = buildFilterQuery()
  queryVals.first_key = first_key.value
  router.push({ query: queryVals })
}

const changePerPage = (event) => {
  const queryVals = buildFilterQuery()
  queryVals.per_page = event.target.value
  per_page.value = queryVals.per_page

  if (route.query.last_key !== undefined) {
    queryVals.last_key = route.query.last_key
  } else if (route.query.first_key !== undefined) {
    queryVals.first_key = route.query.first_key
  }

  router.push({ query: queryVals })
}

// Load metrics data
const loadMetrics = async () => {
  try {
    syncQueryToFilters()
    let mode = 'normal'
    let urlString = '/app/metric/list?'

    if (route.query.last_key !== undefined) {
      urlString += '&last_key=' + encodeURIComponent(route.query.last_key)
      show_back.value = true
      mode = 'normal'
    } else if (route.query.first_key !== undefined) {
      urlString += '&first_key=' + encodeURIComponent(route.query.first_key)
      has_more.value = true
      mode = 'first_key'
    }

    if (route.query.per_page !== undefined) {
      urlString += '&per_page=' + route.query.per_page
    }

    Object.keys(filters).forEach(key => {
      if (route.query[key] !== undefined) {
        urlString += '&' + key + '=' + encodeURIComponent(route.query[key])
      }
    })

    loaded.value = false
    const response = await get(urlString)

    metrics.value = response.data.data
    if (mode === 'normal') {
      has_more.value = response.data.has_more
    } else {
      show_back.value = response.data.has_more
      has_more.value = true
    }
    
    last_key.value = response.data.last_key
    first_key.value = response.data.first_key
    ready.value = true
    loaded.value = true
  } catch (error) {
    has_error.value = true
  }
}

// Filter toggle functionality
const toggle = (key) => {
  const index = active_filters.value.indexOf(key)
  if (index === -1) {
    active_filters.value.push(key)
  } else {
    active_filters.value.splice(index, 1)
  }
}

// Check if filter is active
const isActive = (key) => {
  return active_filters.value.includes(key)
}

// Lifecycle hooks
onMounted(() => {
  loadMetrics()
})

// Watch for route query changes
watch(() => route.query, () => {
  loadMetrics()
})

// Define exposed functions for the template (fixing typo in original)
defineExpose({
  currencyFormat,
  loadMetrics,
  nextPage,
  prevPage,
  changePerPage,
  doSearch,
  toggle,
  isActive
})
</script>

<style scoped>

</style>
