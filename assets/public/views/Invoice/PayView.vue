<template>
  <div class="mt-5">
    <h1 class="text-xl3">{{ $t('portal.invoice.pay.title') }}</h1>
    <div v-if="ready">
      <div v-if="not_found">
        {{ $t('portal.invoice.pay.not_found') }}
      </div>
      <div v-else-if="general_error">
        {{ $t('portal.invoice.pay.general_error') }}
      </div>
      <div v-else-if="invoice.paid">
        {{ $t('portal.invoice.pay.payment.already_paid')}}
      </div>
      <div v-else>
        <div class="mt-5">{{ $t('portal.invoice.pay.payment.amount', {amount: displayCurrency(invoice.amount), currency: invoice.currency}) }}</div>


        <form @submit.prevent="send" :disabled="sending">

          <div id="cardInput" class="my-5"></div>
          <div id="cardError"></div>

          <div class="mt-5 text-center">
            <SubmitButton @click="send" :in-progress="sending">{{ $t('portal.invoice.pay.payment.pay_button') }}</SubmitButton>
          </div>
        </form>
      </div>
    </div>
    <div v-else>{{ $t('portal.invoice.pay.loading') }}</div>
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
      invoice: {},
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

    axios.get("/public/invoice/"+hash+"/pay").then(response => {
      this.invoice = response.data.invoice;
      this.stripeConfig = response.data.stripe;
      this.ready = true;
      this.stripe = Stripe(this.stripeConfig.key);
      var that = this;
      if (this.invoice.paid === false) {
        setTimeout(()=> {
          that.card = stripeservice.getCardToken(that.stripe, that.stripeConfig.token);
        }, 500)
      }
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
                that.invoice.paid = true;
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