<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-3 card-body">
          <h2 class="mb-3">{{ $t('app.customer.view.main.title') }}</h2>
          <dl>
            <div class="bg-gray-50 rounded-t-xl px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.main.email') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.email }}</dd>
            </div>
            <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.main.reference') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.reference }}</dd>
            </div>
            <div class="bg-gray-50 rounded-b-xl px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.main.external_reference') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                <a v-if="customer.payment_provider_details_url" target="_blank" :href="customer.payment_provider_details_url">{{ customer.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ customer.external_reference }}</span>
              </dd>
            </div>
          </dl>

        </div>
        <div class="mt-3 card-body">
          <h2 class="mb-3">{{ $t('app.customer.view.address.title') }}</h2>
          <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.address.street_line_one') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.address.street_line_one }}</dd>
            </div>
            <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.address.street_line_two') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.address.street_line_two }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.address.city') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.address.city }}</dd>
            </div>
            <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.address.region') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.address.region }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.address.country') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.address.country }}</dd>
            </div>
            <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.customer.view.address.post_code') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ customer.address.post_code }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerView",
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      customer: {
      }
    }
  },
  mounted() {
    var customerId = this.$route.params.id
    axios.get('/app/customer/'+customerId).then(response => {
      this.customer = response.data.customer;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
          this.errorMessage = this.$t('app.customer.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.customer.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>