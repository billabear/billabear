<template>
  <div>
    <h1 class="page-title">{{ $t('app.subscription.create.title') }}</h1>

    <form @submit.prevent="send">

      <div class="mt-3 card-body">
        <h2>{{ $t('app.subscription.create.subscription_plans') }}</h2>

        <div>
          <select class="form-field" v-model="subscription_plan">
            <option :value="null"></option>
            <option v-for="subscriptionPlan in subscription_plans" :value="subscriptionPlan">{{ subscriptionPlan.product.name }} - {{ subscriptionPlan.name }}</option>
          </select>
        </div>
      </div>

      <div class="mt-3 card-body">
        <h2>{{ $t('app.subscription.create.prices') }}</h2>

        <div>
          <select class="form-field" :disabled="subscription_plan == null || eligiblePrices.length == 0">
            <option v-for="price in eligiblePrices" :value="price.id">{{ price.display_value }} - {{ price.schedule }}</option>
            <option v-if="subscription_plan == null" selected></option>
            <option v-else-if="eligiblePrices.length == 0" selected>{{ $t('app.subscription.create.no_eligible_prices') }}</option>
          </select>
        </div>
      </div>

      <div class="mt-3 card-body">
        <h2>{{ $t('app.subscription.create.payment_details') }}</h2>

        <div>
          <select class="form-field">
            <option v-for="paymentDetail in payment_details" :value="paymentDetail.id">{{ paymentDetail.last_four }} - {{ paymentDetail.expiry_month }}/{{ paymentDetail.expiry_year }}</option>
          </select>
        </div>
      </div>
      <div class="form-field-submit-ctn">
        <SubmitButton :in-progress="sendingInProgress">{{ $t('app.subscription.create.submit_btn') }}</SubmitButton>
      </div>
      <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.subscription.create.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SubscriptionPlanCreate",
  data() {
    return {
      subscription_plan: null,
      subscription_plans: [],
      payment_details: [],
      sendingInProgress: false,
      eligible_currency: null,
      showAdvance: false,
      success: false,
      errors: {
      },
      product: {},
      prices: [],
      features: []
    }
  },
  computed: {
    eligiblePrices() {
      if (this.subscription_plan === null) {
        return [];
      }

      if (this.eligible_currency === null) {
        return this.subscription_plan.prices;
      }

      var output = [];

      for (var i = 0; i < this.subscription_plan.prices.length; i++) {
        if (this.subscription_plan.prices[i].currency == this.eligible_currency) {
          output.push(this.subscription_plan.prices[i]);
        }
      }

      return output;
    }
  },
  mounted() {

    var customerId = this.$route.params.customerId
    this.id = customerId;
    axios.get('/app/customer/'+customerId+'/subscription').then(response => {
      this.subscription_plans = response.data.subscription_plans;
      this.payment_details = response.data.payment_details;
      this.eligible_currency = response.data.eligible_currency;
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
    removeFeature: function (key) {
      this.subscription_plan.features.splice(key, 1);
    },
    removeLimit: function (key) {
      this.subscription_plan.limits.splice(key, 1);
    },
    removePrice: function (key) {
      this.subscription_plan.prices.splice(key, 1);
    },
    send: function () {

    }
  }
}
</script>

<style scoped>
</style>