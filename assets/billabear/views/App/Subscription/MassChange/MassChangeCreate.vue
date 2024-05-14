<template>
  <div>
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.subscription.mass_change.create.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="p-5">
        <div class="mt-3 card-body">
          <h2>{{ $t('app.subscription.mass_change.create.criteria.title') }}</h2>

          <div class="mt-5">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription.mass_change.create.criteria.plan') }}
            </label>
            <p class="form-field-error" v-if="errors['targetPlan'] != undefined">{{ errors['targetPlan'] }}</p>
            <select class="form-field" v-model="payload.target_plan" @change="">
              <option :value="null"></option>
              <option v-for="subscriptionPlan in plans" :value="subscriptionPlan">{{ subscriptionPlan.product.name }} - {{ subscriptionPlan.name }}</option>
            </select>
          </div>
          <div class="mt-5">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription.mass_change.create.criteria.price') }}
            </label>
            <p class="form-field-error" v-if="errors['targetPrice'] != undefined">{{ errors['targetPrice'] }}</p>
            <select class="form-field" v-model="payload.target_price" @change="">
              <option :value="null"></option>
              <option v-for="price in targetPrices" :value="price">{{ price.display_value }} / {{ price.schedule }}</option>
            </select>
          </div>
          <div class="mt-5">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription.mass_change.create.criteria.brand') }}
            </label>
            <p class="form-field-error" v-if="errors['targetBrand'] != undefined">{{ errors['targetBrand'] }}</p>
            <select class="form-field" v-model="payload.target_brand" @change="">
              <option :value="null"></option>
              <option v-for="brand in brands" :value="brand">{{ brand.name }}</option>
            </select>
          </div>
          <div class="mt-5">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription.mass_change.create.criteria.country') }}
            </label>
            <p class="form-field-error" v-if="errors['targetCountry'] != undefined">{{ errors['targetCountry'] }}</p>
            <CountrySelect v-model="payload.target_country" />
          </div>
        </div>
        <div class="mt-3 card-body">
          <h2>{{ $t('app.subscription.mass_change.create.new.title') }}</h2>

          <div class="mt-5">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription.mass_change.create.new.plan') }}
            </label>
            <p class="form-field-error" v-if="errors['newPlan'] != undefined">{{ errors['newPlan'] }}</p>
            <select class="form-field" v-model="payload.new_plan" @change="fetchEstimate">
              <option :value="null"></option>
              <option v-for="subscriptionPlan in newPlans" :value="subscriptionPlan">{{ subscriptionPlan.product.name }} - {{ subscriptionPlan.name }}</option>
            </select>
          </div>
          <div class="mt-5">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription.mass_change.create.criteria.price') }}
            </label>
            <p class="form-field-error" v-if="errors['newPrice'] != undefined">{{ errors['newPrice'] }}</p>
            <select class="form-field" v-model="payload.new_price" @change="">
              <option :value="null"></option>
              <option v-for="price in newPrices" :value="price">{{ price.display_value }} / {{ price.schedule }}</option>
            </select>
          </div>
        </div>

        <div class="mt-3 card-body">
          <h2>{{ $t('app.subscription.mass_change.create.change_date.title') }}</h2>
          <p>{{ $t('app.subscription.mass_change.create.change_date.help_info') }}</p>

          <p class="form-field-error" v-if="errors['changeDate'] != undefined">{{ errors['changeDate'] }}</p>
          <VueDatePicker  class="mt-2" v-model="payload.change_date"  :enable-time-picker="false" ></VueDatePicker>
        </div>

        <div class="mt-5 card-body" v-if="estimate !== null">
          {{ $t('app.subscription.mass_change.create.estimate.amount', {amount: currency(this.estimate.amount), currency: this.estimate.currency, schedule: this.estimate.schedule}) }}
        </div>

        <div class="mt-5">

          <SubmitButton :in-progress="sending" @click="sendCreate">{{ $t('app.subscription.mass_change.create.submit_button') }}</SubmitButton>
        </div
      ></div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import CountrySelect from "../../../../components/app/Forms/CountrySelect.vue";
import currency from "currency.js";

export default {
  name: "MassChangeCreate",
  components: {CountrySelect},
  data() {
    return {
      ready: false,
      sending: false,
      errors: {},
      prices: [],
      plans: [],
      brands: [],
      estimate: null,
      payload: {
        target_plan: null,
        target_price: null,
        target_brand: null,
        target_country: null,
        new_plan: null,
        new_price: null,
      }
    }
  },
  computed: {
    targetPrices: function () {
      if (this.payload.target_plan == null) {
        return this.prices;
      }
      return this.payload.target_plan.prices;
    },
    newPlans: function () {
      if (this.payload.target_plan == null) {
        return [];
      }
      return this.plans;
    },
    newPrices: function () {
      if (this.payload.new_plan !== null) {
        var prices = [];
        const len = this.prices.length;
        for (let i = 0; i < len; i++) {
          if (this.prices[i].product.id == this.payload.new_plan.product.id) {
            prices.push(this.prices[i]);
          }
        }

        return prices;
      }

      if (this.payload.target_price !== null && this.payload.target_plan == null){
        var prices = [];
        const len = this.prices.length;
        for (let i = 0; i < len; i++) {
          if (this.prices[i].product.id == this.payload.target_price.product.id) {
            prices.push(this.prices[i]);
          }
        }

        return prices;
      }

      if (this.payload.target_plan == null) {
        return [];
      }


      return this.payload.target_plan.prices;
    }
  },
  mounted() {
    axios.get("/app/subscription/mass-change/create").then(response => {
      this.ready = true;
      this.brands = response.data.brands;
      this.plans = response.data.plans;
      this.prices = response.data.prices;
    })
  },
  watch: {
    'payload.new_price': function () {
      this.sendEstimate();
    }
  },
  methods: {
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
    buildPayload: function () {
      const payload = {};
      if (this.payload.target_plan) {
        payload.target_plan = this.payload.target_plan.id;
      }

      if (this.payload.target_price) {
        payload.target_price = this.payload.target_price.id;
      }

      if (this.payload.new_plan) {
        payload.new_plan = this.payload.new_plan.id;
      }

      if (this.payload.new_price) {
        payload.new_price = this.payload.new_price.id;
      }

      if (this.payload.target_brand) {
        payload.target_brand = this.payload.target_brand.code;
      }

      if (this.payload.target_country) {
        payload.target_country = this.payload.target_country;
      }

      return payload;
    },
    sendEstimate: function () {
      const payload = this.buildPayload();
      this.errors = {}
      axios.post("/app/subscription/mass-change/estimate", payload).then(response => {
        this.sending = false;
        this.estimate = response.data;
      }).catch(error => {

        if (error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.unknown_error = true;
        }
        this.sending = false;
      })
    },
    sendCreate: function () {

      this.errors = {};
      this.sending = true;

      const payload = this.buildPayload();
      if (this.payload.change_date) {
        payload.change_date = this.payload.change_date;
      }

      axios.post("/app/subscription/mass-change", payload).then(response => {

        this.sending = false;
        this.$router.push({name: 'app.subscription.mass_change.view', params: {id: response.data.id}})
      }).catch(error => {

        if (error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.unknown_error = true;
        }
        this.sending = false;
      })

    }
  }
}
</script>

<style scoped>

</style>