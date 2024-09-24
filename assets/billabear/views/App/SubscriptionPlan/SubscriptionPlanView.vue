<template>
  <div>
    <div class="grid grid-cols-2">

      <h1 class="page-title">{{ $t('app.subscription_plan.view.title') }}</h1>
      <div class="text-end mt-5">

        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <div class="text-end">
            <router-link :to="{name: 'app.subscription_plan.update', params: {productId: product_id, subscriptionPlanId: subscription_plan.id}}" class="btn--main">{{ $t('app.customer.view.update') }}</router-link>
          </div>
        </RoleOnlyView>
      </div>
    </div>

    <LoadingScreen :ready="ready">
      <div v-if="!error">

        <div class="card-body">
          <h2 class="section-header">{{ $t('app.subscription_plan.view.main.title') }}</h2>
          <div class="section-body">
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.name') }}</dt>
                <dd>{{ subscription_plan.name }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.code_name') }}</dt>
                <dd>{{ subscription_plan.code_name }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.user_count') }}</dt>
                <dd>{{ subscription_plan.user_count }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.public') }}</dt>
                <dd>{{ subscription_plan.public }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.per_seat') }}</dt>
                <dd>{{ subscription_plan.per_seat }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.free') }}</dt>
                <dd>{{ subscription_plan.free }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.has_trial') }}</dt>
                <dd>{{ subscription_plan.has_trial }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.trial_length_days') }}</dt>
                <dd>{{ subscription_plan.trial_length_days }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription_plan.view.main.is_trial_standalone') }}</dt>
                <dd>{{ subscription_plan.is_trial_standalone }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <div class="mt-3">
          <h2 class="mb-3 text-2xl">{{ $t('app.subscription_plan.view.price.title') }}</h2>

          <div class="rounded-lg bg-white shadow p-3">
            <table class="w-full">
              <thead>
              <tr class="border-b border-black">
                <th class="text-left pb-2">{{ $t('app.subscription_plan.view.price.list.amount') }}</th>
              <th class="text-left pb-2">{{ $t('app.subscription_plan.view.price.list.currency') }}</th>
              <th class="text-left pb-2">{{ $t('app.subscription_plan.view.price.list.recurring') }}</th>
              <th class="text-left pb-2">{{ $t('app.subscription_plan.view.price.list.including_tax') }}</th>
              <th class="text-left pb-2">{{ $t('app.subscription_plan.view.price.list.public') }}</th>
              <th class="text-left pb-2">{{ $t('app.subscription_plan.view.price.list.external_reference') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="price in subscription_plan.prices" class="mt-5">
              <td class="py-3">{{ price.display_value }}</td>
              <td>{{ price.currency }}</td>
              <td>{{ price.recurring }}</td>
              <td>{{ price.including_tax }}</td>
              <td>{{ price.public }}</td>
              <td>
                <a v-if="price.payment_provider_details_url" target="_blank" :href="price.payment_provider_details_url">{{
                    price.external_reference
                  }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ price.external_reference }}</span>
              </td>
            </tr>
            <tr v-if="subscription_plan.prices.length === 0">
              <td colspan="7" class="py-3 text-center">{{ $t('app.product.view.price.no_prices') }}</td>
            </tr>
            </tbody>
          </table>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">

          <div class="mt-5">
            <h2 class="mb-3 text-2xl">{{ $t('app.subscription_plan.view.limits.title') }}</h2>

            <div class="rounded-lg bg-white shadow p-3">
              <table class="w-full">
                <thead>
                <tr class="border-b border-black">
                  <th class="text-left pb-2">{{ $t('app.subscription_plan.view.limits.list.feature') }}</th>
                <th class="text-left pb-2">{{ $t('app.subscription_plan.view.limits.list.limit') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="limit in subscription_plan.limits" class="mt-5">
                <td class="py-3">{{ limit.feature.name }}</td>
                <td>{{ limit.limit }}</td>
              </tr>
              <tr v-if="subscription_plan.limits.length === 0">
                <td colspan="2" class="py-3 text-center">{{ $t('app.subscription_plan.view.limits.list.no_limits') }}</td>
              </tr>
              </tbody>
            </table>
            </div>
          </div>

          <div class="mt-5">
            <h2 class="mb-3 text-2xl">{{ $t('app.subscription_plan.view.features.title') }}</h2>

            <div class="rounded-lg bg-white shadow p-3">
              <table class="w-full">
                <thead>
                <tr class="border-b border-black">
                  <th class="text-left pb-2">{{ $t('app.subscription_plan.view.features.list.feature') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="feature in subscription_plan.features" class="mt-5">
                <td class="py-3">{{ feature.name }}</td>
              </tr>
              <tr v-if="subscription_plan.features.length === 0">
                <td class="text-center py-3">{{ $t('app.subscription_plan.view.features.list.no_features') }}</td>
              </tr>
              </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

  </div>
</template>

<script>
import axios from "axios";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "SubscriptionPlanView",
  components: {RoleOnlyView},
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      subscription_plan: {
      },
      product_id: null,
    }
  },
  mounted() {
    this.product_id = this.$route.params.productId
    var subscriptionPlanId = this.$route.params.subscriptionPlanId;
    axios.get('/app/product/'+this.product_id+'/plan/'+subscriptionPlanId).then(response => {
      this.subscription_plan = response.data.subscription_plan;
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
