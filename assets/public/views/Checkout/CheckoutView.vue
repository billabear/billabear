<template>

  <div class="h-screen flex flex-col lg:flex-row" v-if="ready">
    <!-- Left Section -->
    <div class="bg-teal-500 w-full lg:w-1/2 h-full desktop-only text-center text-white pt-12">
      <img src="/images/app-logo.svg" alt="BillaBear" class="w-72 mx-auto" />

      <h1 class="my-3 text-4xl underline">{{ $t('portal.checkout.title') }}</h1>

        <div class="mt-5 mx-auto max-w-2xl text-left bg-white text-black p-3 rounded-lg">
          <h2 class="text-3xl">{{
              $t('portal.checkout.total', {amount: displayCurrency(checkout_session.amount_due), currency: checkout.currency})
                  }}</h2>
          <h3 class="text-2xl" v-if="checkout_session.tax_total !== null">{{ $t('portal.checkout.tax_total', {amount: displayCurrency(checkout_session.tax_total), currency: checkout.currency}) }}</h3>

          <h3 class="text-xl mt-5 mb-2">{{ $t('portal.checkout.items.title') }}</h3>
          <div v-for="line in checkout.lines" class="item-line">
            <p>{{ displayCurrency(line.total) }} {{ line.currency }} {{ $t('portal.checkout.schedule.'+line.schedule) }} - {{ line.description }}</p>
          </div>
        </div>
    </div>
        <div class="p-12 w-1/2">
          <div v-if="stage === 'customer'">
            <h2 class="text-2xl font-bold mb-5">{{ $t('portal.checkout.customer.title') }}</h2>
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="email">
                {{ $t('portal.checkout.customer.fields.email') }}
              </label>
              <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
              <input type="email" class="rounded-lg p-2 border-gray-300 text-gray-900 shadow w-full" id="email" v-model="customer.email" />
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="country">
                {{ $t('portal.checkout.customer.fields.country') }}
              </label>
              <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
              <CountrySelect class="rounded-lg p-2 border-gray-300 text-gray-900 shadow-lg w-full"  v-model="customer.address.country" />
            </div>

            <div class="form-field-ctn mt-2">
              <SubmitButton :in-progress="sending" class="w-full shadow-lg btn--main" @click="createCustomer">{{ $t('portal.checkout.customer.submit') }}</SubmitButton>
            </div>
          </div>
          <div v-else-if="stage == 'payment'">

            <h2 class="text-xl mb-5">{{ $t('portal.checkout.payment.title') }}</h2>
            <div id="cardInput" class="my-5"></div>
            <div id="cardError"></div>
            <SubmitButton :in-progress="sending" class="w-full btn--main" @click="createPayment">{{ $t('portal.checkout.customer.submit') }}</SubmitButton>
          </div>
          <div v-else>
            <h2 class="text-xl mb-5">{{ $t('portal.checkout.success.title') }}</h2>

            <p>{{ $t('portal.checkout.success.message') }}</p>
          </div>
        </div>
  </div>
  <div class="flex justify-center items-center h-screen" v-else>
    <img src="/images/public-logo.svg" class="w-80 animate-fade-in-out" />
  </div>

</template>

<script>
import axios from "axios";
import {stripeservice} from "../../../billabear/services/stripeservice";
import currency from "currency.js";
import CountrySelect from "../../../billabear/components/app/Forms/CountrySelect.vue";
import {billingservice} from "../../../billabear/services/billingservice";

export default {
  name: "CheckoutView",
  components: {CountrySelect},
  data() {
    return {
      error_page: false,
      stage: 'customer',
      checkout: {},
      customer: {address: {}},
      ready: false,
      errors: {},
      sending: false,
      stripe: null,
      stripeConfig: {},
      checkout_session: {
        id: null,
        amount_due: null,
        tax_total: null,
        sub_total: null,
      },
      card: {}
    }
  },
  mounted() {
    const slug = this.$route.params.slug;
    axios.get("/public/checkout/"+slug+"/view").then(response => {
      this.ready = true;
      this.checkout = response.data.checkout;
      this.checkout_session.amount_due = this.checkout.total;
      this.checkout_session.tax_total = this.checkout.tax_total;
      this.checkout_session.sub_total = this.checkout.sub_total;
    }).catch(error => {
      this.ready = true;
      if (error.response !== undefined && error.response.status === 404) {
        this.error_page = true;
        return;
      }
      this.error_page = true;
    })
  },
  methods: {
    displayCurrency: function (value) {
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
    createCustomer: function () {

      const slug = this.$route.params.slug;

      this.sending = true;
      var imported = document.createElement('script');
      imported.src = 'https://js.stripe.com/v3/';
      document.head.appendChild(imported);

      axios.post("/public/checkout/"+slug+"/customer", this.customer).then(response => {
        this.stage = 'payment';
        this.stripeConfig = response.data.stripe;
        this.checkout_session = response.data.checkout_session;
        this.ready = true;
        this.stripe = Stripe(this.stripeConfig.key);
        this.sending = false;
        var that = this;
        setTimeout(()=> {
          that.card = stripeservice.getCardToken(that.stripe, that.stripeConfig.token);
        }, 500)
      })
    },
    createPayment: function () {
      const slug = this.$route.params.slug;
      this.sending = true;
      var that = this
      stripeservice.sendCard(this.stripe, this.card).then(
          response => {
            var token = response.token.id;
            const hash = this.$route.params.hash;
            billingservice.portalCheckoutPay(slug, this.checkout_session.id, token).then(response => {
              if (response.data.success) {
                that.stage = 'success';
              } else {
                this.general_error = true;
              }
              that.sending = false;
            })
          }
      )
    }
  }
}
</script>

<style scoped>
</style>
