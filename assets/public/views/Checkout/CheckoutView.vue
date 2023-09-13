<template>
  <div class="mt-5">
    <div class="w-full mb-5 mt-5">
      <img src="/images/app-logo.png" alt="" class="" width="175" />
    </div>
    <div v-if="ready">
      <div class="grid grid-cols-2 gap-4" v-if="!error_page">
        <div class="mt-5 rounded-xl basket-container">
          <h2 class="text-4xl">{{ displayCurrency(checkout_session.amount_due) }} {{ checkout.currency }}</h2>
          <h3 class="text-2xl" v-if="checkout_session.tax_total !== null">{{ $t('portal.checkout.total', {amount: displayCurrency(checkout_session.tax_total), currency: checkout.currency}) }}</h3>

          <h3 class="text-xl mt-5 mb-2">{{ $t('portal.checkout.items.title') }}</h3>
          <div v-for="line in checkout.lines" class="item-line">
            <p>{{ displayCurrency(line.total) }} {{ line.currency }} - {{ line.description }}</p>
          </div>

        </div>
        <div class="mt-5">
          <div v-if="stage === 'customer'">
            <h2 class="text-xl mb-5">{{ $t('portal.checkout.customer.title') }}</h2>
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="email">
                {{ $t('portal.checkout.customer.fields.email') }}
              </label>
              <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
              <input type="email" class="form-field-input w-full" id="email" v-model="customer.email" />
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="country">
                {{ $t('portal.checkout.customer.fields.country') }}
              </label>
              <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
              <CountrySelect class="form-field-input w-full"  v-model="customer.address.country" />
            </div>

            <div class="form-field-ctn mt-2">
              <SubmitButton :in-progress="sending" class="w-full btn--main" @click="createCustomer">{{ $t('portal.checkout.customer.submit') }}</SubmitButton>
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
      <div v-else>
        <div class="text-center">
          <img src="/images/error-bear.png" width="250"  class="m-auto" alt="BillaBear - Loading" />
          <p class="text-3xl font-bold">{{ $t('portal.checkout.error') }}</p>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="text-center">
        <img src="/images/bear-with-papers.png" width="250"  class="m-auto" alt="BillaBear - Loading" />
        <p class="text-3xl font-bold">{{ $t('portal.checkout.loading') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import {stripeservice} from "../../../app/services/stripeservice";
import currency from "currency.js";
import CountrySelect from "../../../app/components/app/Forms/CountrySelect.vue";
import {billingservice} from "../../../app/services/billingservice";

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
.basket-container {
  @apply p-5;
  background: #ffe6bf;
}

.form-field-ctn {
  @apply mb-1;
}

.form-field-lbl {
  @apply block;
}

.form-field-input {
  @apply p-2 border rounded-xl;
}

#cardInput {
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 4px;
}

.hide {
  display: none;
}
</style>