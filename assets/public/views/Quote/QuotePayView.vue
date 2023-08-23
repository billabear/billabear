<template>
  <div class="mt-5">
    <h1 class="text-xl6 mb-5">{{ $t('portal.quote.pay.title') }}</h1>
    <div v-if="ready">

      <div class="my-5">
        <table class="table w-full">
          <thead>
            <tr>
              <th class="w-90">{{ $t('portal.quote.pay.lines.description') }}</th>
              <th>{{ $t('portal.quote.pay.lines.tax_rate') }}</th>
              <th>{{ $t('portal.quote.pay.lines.tax_total') }}</th>
              <th>{{ $t('portal.quote.pay.lines.total') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="line in quote.lines">
              <td>{{ line.description }}</td>
              <td class="text-center">{{ line.tax_rate }}</td>
              <td class="text-center">{{ displayCurrency(line.tax_total) }}</td>
              <td class="text-center">{{ displayCurrency(line.total) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="my-5 text-end">
        <strong>{{ $t('portal.quote.pay.totals.total') }}</strong> {{ displayCurrency(quote.total) }}
      </div>

      <div v-if="not_found">
        {{ $t('portal.quote.pay.not_found') }}
      </div>
      <div v-else-if="general_error">
        {{ $t('portal.quote.pay.general_error') }}
      </div>
      <div v-else>
       <form @submit.prevent="send" :disabled="sending">

         <div class="w-1/2 m-auto p-5">
           <h2>{{ $t('portal.quote.pay.payment_details.title') }}</h2>
          <div id="cardInput" class="my-5"></div>
          <div id="cardError"></div>

         </div>
          <div class="mt-5 text-center">
            <SubmitButton @click="send" :in-progress="sending">{{ $t('portal.quote.pay.payment.pay_button') }}</SubmitButton>
          </div>
        </form>
      </div>
    </div>
    <div v-else>{{ $t('portal.quote.pay.loading') }}</div>
  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import {stripeservice} from "../../../app/services/stripeservice";
import {billingservice} from "../../../app/services/billingservice";

export default {
  name: "PayView",
  data() {
    return {
      sending: false,
      ready: false,
      quote: {},
      stripe: {},
      not_found: false,
      general_error: false,
      stripeConfig: {},
      card: {}
    }
  },
  mounted() {
    const hash = this.$route.params.hash;
    var imported = document.createElement('script');
    imported.src = 'https://js.stripe.com/v3/';
    document.head.appendChild(imported);

    axios.get("/public/quote/"+hash+"/pay").then(response => {
      this.quote = response.data.quote;
      this.stripeConfig = response.data.stripe;
      this.ready = true;
      this.stripe = Stripe(this.stripeConfig.key);
      var that = this;

        setTimeout(()=> {
          that.card = stripeservice.getCardToken(that.stripe, that.stripeConfig.token);
        }, 500)
    }).catch(error => {
      console.log(error)
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
    send: function (value) {
      var that = this
      stripeservice.sendCard(this.stripe, this.card).then(
          response => {
            var token = response.token.id;
            const hash = this.$route.params.hash;
            billingservice.portalPay(hash, token).then(response => {
              if (response.data.success) {
                that.quote.paid = true;
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

<style>
.btn--main {
  background-color: black !important;
  color: white;
  padding: 10px;
  border-radius: 5px;
  margin: auto;
}
</style>