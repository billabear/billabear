<template>
  <div v-if="!hasError">
    <div class="grid grid-cols-2">
      <PageTitle>{{ $t('app.customer.list.title') }}</PageTitle>
      <div class="text-end mt-3">
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <router-link :to="{name: 'app.customer.create'}" class="btn--main ml-4">
            <i class="fa-solid fa-user-plus"></i> {{ $t('app.customer.list.create_new') }}
          </router-link>
        </RoleOnlyView>
      </div>
    </div>

    <LoadingScreen :ready="!loading">
      <div class="md:flex">
        <FiltersSection :filters="filters" @apply-filters="applyFilters" @clear-filters="clearFilters" />
        <div class="md:pl-3 md:flex-1">
          <CustomerTable :customers="customers" :loaded="loaded" />
          <CustomerPagination 
            :show-back="showBack"
            :has-more="hasMore"
            :per-page="perPage"
            :loading="loading"
            @prev-page="prevPage"
            @next-page="nextPage"
            @change-per-page="changePerPage"
          />
        </div>
      </div>
    </LoadingScreen>
  </div>
  <div v-else class="error-page">
    {{ $t('app.customer.list.error_message') }}
  </div>
</template>

<script>
import { onMounted } from 'vue'
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";
import PageTitle from "../../../components/app/Ui/Typography/PageTitle.vue";
import FiltersSection from "../../../components/app/Ui/Section/FiltersSection.vue";
import CustomerTable from "../../../components/app/Customer/CustomerTable.vue";
import CustomerPagination from "../../../components/app/Customer/CustomerPagination.vue";
import { useCustomerApi } from "../../../composables/useCustomerApi.js";

export default {
  name: "CustomerList",
  components: {
    RoleOnlyView,
    PageTitle,
    FiltersSection,
    CustomerTable,
    CustomerPagination
  },
  setup() {
    const {
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
      fetchCustomers,
      nextPage,
      prevPage,
      changePerPage,
      applyFilters,
      clearFilters
    } = useCustomerApi()

    onMounted(() => {
      fetchCustomers()
    })

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
}
</script>

<style scoped>
</style>
