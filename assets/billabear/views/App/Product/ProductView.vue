<template>
  <div>
    <div class="grid grid-cols-2">

      <h1 class="page-title">{{ $t('app.product.view.title') }}</h1>

      <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">

        <div class="mt-5 text-end">
          <router-link :to="{name: 'app.product.update'}" class="btn--main">{{ $t('app.product.view.update') }}</router-link>
        </div>
      </RoleOnlyView>
    </div>

    <LoadingScreen :ready="ready">
      <div v-if="!error">

        <div class="card-body">
          <h2 class="section-header">{{ $t('app.product.view.main.title') }}</h2>
          <div class="section-body">

            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.product.view.main.name') }}</dt>
                <dd>{{ product.name }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.product.view.main.physical') }}</dt>
                <dd>{{ product.physical }}</dd>
              </div>
              <div v-if="product.tax_rate">
                <dt>{{ $t('app.product.view.main.tax_rate') }}</dt>
                <dd>{{ product.tax_rate }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.product.view.main.tax_type') }}</dt>
                <dd><span v-if="product.tax_type">{{ product.tax_type.name }}</span></dd>
              </div>
              <div>
                <dt>{{ $t('app.product.view.main.external_reference') }}</dt>
                <dd>
                  <a v-if="product.payment_provider_details_url" target="_blank" :href="product.payment_provider_details_url">{{ product.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                  <span v-else>{{ product.external_reference }}</span>
                </dd>
              </div>
            </dl>
          </div>
        </div>

        <div class="mt-4">
          <div class="grid grid-cols-2">

            <h2 class="mb-3 text-2xl font-bold">{{ $t('app.product.view.subscription_plan.title') }}</h2>

            <div class="text-end">

              <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">

                <router-link :to="{name: 'app.subscription_plan.create', params: {productId: id}}" class="btn--main">{{ $t('app.product.view.subscription_plan.create') }}</router-link>

              </RoleOnlyView>
            </div>
          </div>
          <div class="rounded-lg bg-white shadow p-3">
            <table class="w-full">
              <thead>
              <tr class="border-b border-black">
                <th class="text-left pb-2">{{ $t('app.product.view.subscription_plan.list.name') }}</th>
              <th class="text-left pb-2">{{ $t('app.product.view.subscription_plan.list.code_name') }}</th>
              <th class="text-left pb-2">{{ $t('app.product.view.subscription_plan.list.external_reference') }}</th>
              <th></th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(plan, key) in subscriptionPlans" class="mt-5">
              <td class="py-3">{{ plan.name }}</td>
              <td>{{ plan.code_name }}</td>
              <td>
                <a v-if="plan.payment_provider_details_url" target="_blank" :href="plan.payment_provider_details_url">{{ plan.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ plan.external_reference }}</span>
              </td>
              <td>
                <router-link :to="{name: 'app.subscription_plan.view', params: {productId: id, subscriptionPlanId: plan.id}}" class="btn--main">{{ $t('app.product.view.subscription_plan.view') }}</router-link>
              </td>
              <td>
                <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
                  <button class="btn--danger" @click="deleteSubscriptionPlan(plan, key)"><i class="fa-solid fa-trash"></i></button>
                </RoleOnlyView>
              </td>
            </tr>
            <tr v-if="subscriptionPlans.length === 0">
              <td colspan="5" class="py-3 text-center">{{ $t('app.product.view.subscription_plan.no_subscription_plans') }}</td>
            </tr>
            </tbody>
          </table>

          </div>
        </div>

        <div class="mt-4">
          <div class="grid grid-cols-2">

            <h2 class="mb-3 text-2xl font-bold">{{ $t('app.product.view.price.title') }}</h2>
            <div class="text-end">

              <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
                <router-link :to="{name: 'app.price.create', params: {productId: id}}" class="btn--main">{{ $t('app.product.view.price.create') }}</router-link>
              </RoleOnlyView>
            </div>
          </div>

          <div class="rounded-lg bg-white shadow p-3">
            <table class="w-full">
              <thead>
              <tr class="border-b border-black">
                <th class="text-left pb-2">{{ $t('app.product.view.price.list.amount') }}</th>
              <th>{{ $t('app.product.view.price.list.currency') }}</th>
                <th>{{ $t('app.product.view.price.list.recurring') }}</th>
                <th>{{ $t('app.product.view.price.list.usage') }}</th>
              <th>{{ $t('app.product.view.price.list.including_tax') }}</th>
              <th>{{ $t('app.product.view.price.list.public') }}</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(price, key) in prices" class="mt-5">
              <td>{{ price.display_value }}</td>
              <td>{{ price.currency }}</td>
              <td>{{ price.recurring }}</td>
              <td>{{ price.usage }}</td>
              <td>{{ price.including_tax }}</td>
              <td>{{ price.public }}</td>
              <td>
                <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
                  <button class="btn--main" :title="$t('app.product.view.price.show')" @click="showPrice(price, key)" v-if="!price.public"><i class="fa-solid fa-eye"></i></button>
                  <button class="btn--main" :title="$t('app.product.view.price.hide')" @click="hidePrice(price, key)" v-else><i class="fa-solid fa-eye-slash"></i></button>
                  <button class="ml-2 btn--danger" @click="deletePrice(price, key)"><i class="fa-solid fa-trash"></i></button>
                </RoleOnlyView>
              </td>
            </tr>
            <tr v-if="prices.length === 0">
              <td colspan="8" class="text-center">{{ $t('app.product.view.price.no_prices') }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
      </div>

      <div v-else>
        <ErrorBear />
      </div>
    </LoadingScreen>

  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";
import ErrorBear from "../../../components/app/ErrorBear.vue";

export default {
  name: "productView",
  components: {ErrorBear, RoleOnlyView},
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
  },
  methods: {
    deleteSubscriptionPlan: function (plan, key) {
      var productId = this.$route.params.id
      axios.delete('/app/product/'+productId+'/plan/'+plan.id).then(response => {
        this.subscriptionPlans.splice(key,1)
      }).catch(error => {
        alert(this.$t("app.product.view.error_delete"))
      })
    },
    deletePrice: function (price, key) {
      var productId = this.$route.params.id
      axios.post('/app/product/'+productId+'/price/'+price.id+'/delete').then(response => {

        this.prices.splice(key,1)
      }).catch(error => {
        alert(this.$t("app.product.view.error_delete"))
      })
    },
    showPrice: function (price, key) {
      var productId = this.$route.params.id
      axios.post('/app/product/'+productId+'/price/'+price.id+'/public').then(response => {

        price.public = true;
      }).catch(error => {
        alert(this.$t("app.product.view.error_delete"))
      })
    },
    hidePrice: function (price, key) {
      var productId = this.$route.params.id
      axios.post('/app/product/'+productId+'/price/'+price.id+'/private').then(response => {

        price.public = false;
      }).catch(error => {
        alert(this.$t("app.product.view.error_delete"))
      })
    },
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
  }
}
</script>

<style scoped>

</style>
