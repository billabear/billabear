<template>
  <div>
    <h1 class="page-title">{{ $t('app.product.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-3 text-end">
          <router-link :to="{name: 'app.product.update'}" class="btn--main">{{ $t('app.product.view.update') }}</router-link>
        </div>

        <div class="mt-5 card-body">
          <h2 class="mb-3">{{ $t('app.product.view.main.title') }}</h2>
          <dl>
            <div class="bg-gray-50 rounded-t-xl px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.main.name') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ product.name }}</dd>
            </div>
            <div class="bg-gray-50 rounded-b-xl px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.main.external_reference') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                <a v-if="product.payment_provider_details_url" target="_blank" :href="product.payment_provider_details_url">{{ product.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ product.external_reference }}</span>
              </dd>
            </div>
          </dl>
        </div>

        <div class="mt-3 card-body">
          <h2 class="mb-3">{{ $t('app.product.view.address.title') }}</h2>
          <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.address.street_line_one') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ product.address.street_line_one }}</dd>
            </div>
            <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.address.street_line_two') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ product.address.street_line_two }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.address.city') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ product.address.city }}</dd>
            </div>
            <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.address.region') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ product.address.region }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.address.country') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ product.address.country }}</dd>
            </div>
            <div class="bg-gray-100 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">{{ $t('app.product.view.address.post_code') }}</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ product.address.post_code }}</dd>
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
  name: "productView",
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      product: {
      }
    }
  },
  mounted() {
    var productId = this.$route.params.id
    axios.get('/app/product/'+productId).then(response => {
      this.product = response.data.product;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
          this.errorMessage = this.$t('app.product.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.product.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>