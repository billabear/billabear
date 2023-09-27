<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.subscription.create.title') }}</h1>

    <div class="alert-error" v-if="unknown_error">
      {{ $t('app.subscription.create.unknown_error') }}
    </div>

    <form @submit.prevent="send">
      <div class="p-5">
        <div class="mt-3 card-body">
          <h2>{{ $t('app.subscription.create.subscription_plans') }}</h2>

          <div>
            <select class="form-field" v-model="subscription_plan" @change="refreshTrial">
              <option :value="null"></option>
              <option v-for="subscriptionPlan in subscription_plans" :value="subscriptionPlan">{{ subscriptionPlan.product.name }} - {{ subscriptionPlan.name }}</option>
            </select>
          </div>
        </div>

        <div class="mt-3 card-body">
          <h2>{{ $t('app.subscription.create.prices') }}</h2>

          <div>
            <select class="form-field" v-model="price" :disabled="subscription_plan == null || eligiblePrices.length == 0">
              <option v-for="price in eligiblePrices" :value="price.id">{{ price.display_value }} - {{ price.schedule }}</option>
              <option v-if="subscription_plan == null" selected></option>
              <option v-else-if="eligiblePrices.length == 0" selected>{{ $t('app.subscription.create.no_eligible_prices') }}</option>
            </select>
          </div>
          <p class="form-field-help" v-if="eligible_currency != null">{{ $t('app.subscription.create.help_info.eligible_prices') }}</p>
        </div>

        <div class="mt-3 card-body" v-if="subscription_plan != null && subscription_plan.per_seat == true">
          <h2>{{ $t('app.subscription.create.seats') }}</h2>

          <div>
            <p class="form-field-error" v-if="errors['seatNumber'] != undefined">{{ errors['seatNumber'] }}</p>
            <input type="number" v-model="seat_number"  class="form-field" />
          </div>
          <p class="form-field-help" v-if="eligible_currency != null">{{ $t('app.subscription.create.help_info.seats') }}</p>
        </div>

        <div class="mt-3 card-body" v-if="customer.billing_type != 'invoice'">
          <h2>{{ $t('app.subscription.create.payment_details') }}</h2>

          <div>
            <select class="form-field" v-model="payment_detail">
              <option v-for="paymentDetail in payment_details" :value="paymentDetail.id">{{ paymentDetail.last_four }} - {{ paymentDetail.expiry_month }}/{{ paymentDetail.expiry_year }}</option>
            </select>
          </div>
        </div>

        <div class="mt-3 card-body">
          <h2>{{ $t('app.subscription.create.trial') }}</h2>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="country">
              {{ $t('app.subscription.create.trial') }}
            </label>
            <p class="form-field-error" v-if="errors['trial'] != undefined">{{ errors['trial'] }}</p>
            <input type="checkbox"  v-model="trial" :disabled="eligible_currency != null" />
            <p class="form-field-help" v-if="eligible_currency != null">{{ $t('app.subscription.create.help_info.trial') }}</p>
            <p class="form-field-help" v-if="subscription_plan != null && !subscription_plan.has_trial">{{ $t('app.subscription.create.help_info.no_trial') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="country">
              {{ $t('app.subscription.create.trial_length_days') }}
            </label>
            <input type="number" class="form-field" v-model="trial_length_days" :disabled="eligible_currency != null || !trial" />
          </div>
        </div>
        <div class="form-field-submit-ctn">
          <button type="submit" class="btn--main" disabled v-if="price == null || subscription_plan == null">{{ $t('app.subscription.create.submit_btn') }}</button>
          <SubmitButton :in-progress="sendingInProgress" v-else>{{ $t('app.subscription.create.submit_btn') }}</SubmitButton>
        </div>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.subscription.create.success_message') }}</p>
      </div>
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
      payment_detail: null,
      price: null,
      sendingInProgress: false,
      eligible_currency: null,
      eligible_schedule: null,
      showAdvance: false,
      seat_number: 1,
      success: false,
      errors: {
      },
      product: {},
      customer: {},
      prices: [],
      features: [],
      trial: true,
      trial_length_days: 0,
      unknown_error: false,
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
        if (this.subscription_plan.prices[i].currency == this.eligible_currency && this.subscription_plan.prices[i].schedule == this.eligible_schedule) {
          output.push(this.subscription_plan.prices[i]);
        }
      }

      return output;
    }
  },
  mounted() {

    const customerId = this.$route.params.customerId
    this.id = customerId;
    axios.get('/app/customer/'+customerId+'/subscription').then(response => {
      this.subscription_plans = response.data.subscription_plans;
      this.payment_details = response.data.payment_details;
      this.eligible_currency = response.data.eligible_currency;
      this.eligible_schedule = response.data.eligible_schedule;
      this.customer = response.data.customer;
      if (this.eligible_currency != null) {
        this.trial = false;
      }
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
    refreshTrial: function () {
        if (this.eligible_currency != null) {
          this.trial = false;
          return;
        }
        this.trial = this.subscription_plan.has_trial;
        this.trial_length_days = this.subscription_plan.trial_length_days;
    },
    send: function () {

      if (
          this.price === null ||
          this.subscription_plan === null
      ) {
        return;
      }

      const customerId = this.$route.params.customerId
      const payload = {
        payment_details: this.payment_detail,
        price: this.price,
        subscription_plan: this.subscription_plan.id,
        has_trial: this.trial,
        trial_length_days: this.trial_length_days
      }
      if (this.subscription_plan.per_seat) {
        payload.seat_number = this.seat_number;
      }

      this.sendingInProgress = true;
      axios.post('/app/customer/'+customerId+'/subscription', payload).then(response => {
        this.sendingInProgress = false;
        this.success = true;
        this.$router.push({'name': 'app.subscription.view', params: {subscriptionId: response.data.id}})
      }).catch(error => {
        if (error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.unknown_error = true;
        }
        this.sendingInProgress = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>
</style>