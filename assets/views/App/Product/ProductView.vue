<template>
  <div>
    <h1 class="page-title">{{ $t('app.product.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-3 text-end">
          <router-link :to="{name: 'app.product.update'}" class="btn--main">{{ $t('app.product.view.update') }}</router-link>
        </div>

        <div class="mt-5">
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

        <div class="mt-5">
          <h2 class="mb-3">{{ $t('app.product.view.price.title') }}</h2>

          <table class="list-table">
            <thead>
            <tr>
              <th>{{ $t('app.product.view.price.list.amount') }}</th>
              <th>{{ $t('app.product.view.price.list.currency') }}</th>
              <th>{{ $t('app.product.view.price.list.recurring') }}</th>
              <th>{{ $t('app.product.view.price.list.schedule') }}</th>
              <th>{{ $t('app.product.view.price.list.including_tax') }}</th>
              <th>{{ $t('app.product.view.price.list.public') }}</th>
              <th>{{ $t('app.product.view.price.list.external_reference') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="price in prices" class="mt-5">
              <td>{{ price.amount }}</td>
              <td>{{ price.currency }}</td>
              <td>{{ price.recurring }}</td>
              <td>{{ price.schedule }}</td>
              <td>{{ price.including_tax }}</td>
              <td>{{ price.public }}</td>
              <td>
                <a v-if="price.payment_provider_details_url" target="_blank" :href="price.payment_provider_details_url">{{ price.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ price.external_reference }}</span>
              </td>
            </tr>
            <tr v-if="prices.length === 0">
              <td colspan="7" class="text-center">{{ $t('app.product.view.price.no_prices') }}</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
              <th>{{ $t('app.product.view.price.list.amount') }}</th>
              <th>{{ $t('app.product.view.price.list.currency') }}</th>
              <th>{{ $t('app.product.view.price.list.recurring') }}</th>
              <th>{{ $t('app.product.view.price.list.schedule') }}</th>
              <th>{{ $t('app.product.view.price.list.including_tax') }}</th>
              <th>{{ $t('app.product.view.price.list.public') }}</th>
              <th>{{ $t('app.product.view.price.list.external_reference') }}</th>
            </tr>
            </tfoot>
          </table>

          <router-link :to="{name: 'app.price.create', params: {productId: id}}" class="mt-4 btn--main">{{ $t('app.product.view.price.create') }}</router-link>
        </div>
        <div class="mt-5">
          <h2 class="mb-3">{{ $t('app.product.view.subscription_plan.title') }}</h2>

          <table class="list-table mb-5">
            <thead>
            <tr>
              <th>{{ $t('app.product.view.subscription_plan.list.name') }}</th>
              <th>{{ $t('app.product.view.subscription_plan.list.external_reference') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="plan in subscriptionPlans" class="mt-5">
              <td>{{ plan.name }}</td>
              <td>
                <a v-if="plan.payment_provider_details_url" target="_blank" :href="plan.payment_provider_details_url">{{ plan.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ plan.external_reference }}</span>
              </td>
            </tr>
            <tr v-if="subscriptionPlans.length === 0">
              <td colspan="4" class="text-center">{{ $t('app.product.view.subscription_plan.no_subscription_plans') }}</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
              <th>{{ $t('app.product.view.subscription_plan.list.name') }}</th>
              <th>{{ $t('app.product.view.subscription_plan.list.external_reference') }}</th>
            </tr>
            </tfoot>
          </table>

          <router-link :to="{name: 'app.subscription_plan.create', params: {productId: id}}" class="mt-4 btn--main">{{ $t('app.product.view.subscription_plan.create') }}</router-link>
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
      id: null,
      product: {
      },
      prices: [],
      subscriptionPlans: [],
    }
  },
  mounted() {
    var productId = this.$route.params.id
    this.id = productId;
    axios.get('/app/product/'+productId).then(response => {
      this.product = response.data.product;
      this.prices = response.data.prices;
      this.subscriptionPlans = response.data.subscription_plans;
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