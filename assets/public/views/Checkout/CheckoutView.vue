<template>
  <div class="mt-5">
    <div class="w-full mb-5 mt-5">
      <img src="/images/app-logo.png" alt="" class="" width="175" />
    </div>
    <div class="grid grid-cols-2 gap-4">

      <div class="mt-5 rounded-xl basket-container">
        <h2 class="text-4xl">{{ displayCurrency(amounts.amount_due) }} {{ checkout.currency }}</h2>
        <h3 class="text-2xl" v-if="amounts.tax_total !== null">{{ $t('portal.checkout.total', {amount: displayCurrency(amounts.tax_total), currency: checkout.currency}) }}</h3>

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
            <input type="email" class="form-field-input" id="email" v-model="customer.email" />
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="country">
              {{ $t('portal.checkout.customer.fields.country') }}
            </label>
            <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
            <input type="text" class="form-field-input" id="country"  v-model="customer.address.country"  />
          </div>

          <div class="form-field-ctn mt-2">
            <SubmitButton :in-progress="sending" class="w-full btn--main" @click="createCustomer">{{ $t('portal.checkout.customer.submit') }}</SubmitButton>
          </div>
        </div>
        <div v-else-if="stage == 'payment'">

          <div id="cardInput" class="my-5"></div>
          <div id="cardError"></div>
        </div>


      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import {stripeservice} from "../../../app/services/stripeservice";
import currency from "currency.js";

export default {
  name: "CheckoutView",
  data() {
    return {
      stage: 'customer',
      checkout: {},
      customer: {address: {}},
      ready: false,
      errors: {},
      sending: false,
      stripe: null,
      stripeConfig: {},
      amounts: {
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
      this.checkout = response.data.checkout;
      this.amounts.amount_due = this.checkout.total;
      this.amounts.tax_total = this.checkout.tax_total;
      this.amounts.sub_total = this.checkout.sub_total;
    }).catch(error => {
      if (error.response !== undefined && error.response.status === 404) {
        this.not_found = true;
        return;
      }
      this.general_error = true;
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
        this.amounts.amount_due = response.data.amounts.amount_due;
        this.amounts.tax_total = response.data.amounts.tax_total;
        this.amounts.sub_total = response.data.amounts.sub_total;
        this.ready = true;
        this.stripe = Stripe(this.stripeConfig.key);
        var that = this;
        setTimeout(()=> {
          that.card = stripeservice.getCardToken(that.stripe, that.stripeConfig.token);
        }, 500)
      })
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
</style>