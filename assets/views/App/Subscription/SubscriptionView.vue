<template>
  <div>
    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-5">
          <h2 class="mb-3">{{ $t('app.subscription.view.title') }}</h2>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.subscription.view.main.status') }}</dt>
              <dd>{{ subscription.status }}</dd>
            </div>

            <div>
              <dt>{{ $t('app.subscription.view.main.plan') }}</dt>
              <dd>
                <router-link :to="{name: 'app.subscription_plan.view', params: {productId: product.id, subscriptionPlanId: subscription.plan.id}}">
                  {{ subscription.plan.name }}
                </router-link>
              </dd>
            </div>
            <div>
              <dt>{{ $t('app.subscription.view.main.customer') }}</dt>
              <dd>
                <router-link :to="{name: 'app.customer.view', params: {id: customer.id}}">
                  {{ customer.email }}
                </router-link>
              </dd>
            </div>
            <div>
              <dt>{{ $t('app.subscription.view.main.main_external_reference') }}</dt>
              <dd>
                <a v-if="subscription.external_main_reference_details_url" target="_blank" :href="subscription.external_main_reference_details_url">{{ subscription.main_external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ subscription.main_external_reference }}</span>
              </dd>
            </div>
            <div>
              <dt>{{ $t('app.subscription.view.main.created_at') }}</dt>
              <dd> {{ $filters.moment(subscription.created_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}
              </dd>
            </div>
            <div v-if="subscription.ended_at != null">
              <dt>{{ $t('app.subscription.view.main.ended_at') }}</dt>
              <dd> {{ $filters.moment(subscription.ended_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}
              </dd>
            </div>
            <div v-else>
              <dt>{{ $t('app.subscription.view.main.valid_until') }}</dt>
              <dd> {{ $filters.moment(subscription.valid_until, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}
              </dd>
            </div>
          </dl>
        </div>
        <div class="mt-5">
          <h2 class="mb-3">{{ $t('app.subscription.view.pricing.title') }}</h2>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.subscription.view.pricing.price') }}</dt>
              <dd>{{ subscription.price.display_value }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.subscription.view.pricing.recurring') }}</dt>
              <dd>{{ subscription.price.recurring }}</dd>
            </div>
            <div v-if="subscription.price.recurring">
              <dt>{{ $t('app.subscription.view.pricing.schedule') }}</dt>
              <dd>{{ subscription.price.schedule }}</dd>
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
  name: "SubscriptionView",
  data() {
    return {
      subscription: {},
      customer: {},
      product: {},
      payments: [],
      refunds: [],
      ready: false,
      error: false,
      errorMessage: undefined,
    };
  },
  mounted() {
    var subscriptionId = this.$route.params.subscriptionId
    axios.get('/app/subscription/' + subscriptionId).then(response => {
      this.product = response.data.product;
      this.subscription = response.data.subscription;
      this.customer = response.data.customer;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
        this.errorMessage = this.$t('app.subscription.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.subscription.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>