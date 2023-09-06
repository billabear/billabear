<template>
  <div class="mt-5">
    <div class="w-full mb-5 mt-5">
      <img src="/images/app-logo.png" alt="" class="" width="175" />
    </div>
    <h1 class="text-2xl mb-5">{{ $t('portal.quote.pay.title') }}</h1>
    <div v-if="ready">
      <div v-if="!quote.paid">

        <div class="grid grid-cols-2">
          <div class="w-50">
          </div>

          <div class="w-50 text-end">
            <h3 class="mb-5 font-extrabold">{{ $t('portal.quote.pay.payee_details.title') }}</h3>
            {{ quote.customer.email }} <br v-if="quote.customer.email" />
            {{ quote.customer.address.company_name }} <br v-if="quote.customer.address.company_name" />
            {{ quote.customer.address.street_line_one }}<br  v-if="quote.customer.address.street_line_one" />
            {{ quote.customer.address.street_line_two }}<br v-if="quote.customer.address.street_line_two" />
            {{ quote.customer.address.city }}<br v-if="quote.customer.address.city" />
            {{ quote.customer.address.region }}<br v-if="quote.customer.address.region" />
            {{ quote.customer.address.postcode }}<br v-if="quote.customer.address.postcode" />
          </div>
        </div>
        <div class="my-5 pt-3">
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
                <td>
                  <span v-if="line.subscription_plan === null || line.subscription_plan === undefined">{{ line.description }}</span>
                  <span v-else-if="line.seat_number">{{ line.seat_number }} x {{ line.subscription_plan.name }}</span>
                  <span v-else>{{ line.subscription_plan.name }}</span>
                </td>
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

        <div v-if="!quote.expired">
          <div v-if="quote.expires_at !== null && quote.expires_at !== undefined" class="my-3 text-center text-xl text-red-500">
            {{ $t('portal.quote.pay.expires_at', {date: $filters.moment(quote.created_at, "LLL")}) }}
          </div>
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
        <div v-else class="text-center text-4xl">
          {{ $t('portal.quote.pay.has_expired') }}
        </div>
      </div>
      <div v-else>
        <div class="text-center">
          <img src="/images/bear-with-papers.png" width="250"  class="m-auto" alt="BillaBear - Success" />
          <p class="text-3xl font-bold">{{ $t('portal.quote.pay.already_paid') }}</p>
        </div>
      </div>
    </div>
    <div v-else-if="not_found">
      <div class="text-center">
        <img src="/images/error-bear.png" width="250"  class="m-auto" alt="BillaBear - Error" />
        <p class="text-3xl font-bold">{{ $t('portal.quote.pay.not_found') }}</p>
      </div>
    </div>
    <div v-else-if="general_error">
      <div class="text-center">
        <img src="/images/error-bear.png" width="250"  class="m-auto" alt="BillaBear - Error" />
        <p class="text-3xl font-bold">{{ $t('portal.quote.pay.general_error') }}</p>
      </div>
    </div>
    <div v-else>
      <div class="text-center">
        <img src="/images/bear-with-papers.png" width="250"  class="m-auto" alt="BillaBear - Loading" />
        <p class="text-3xl font-bold">{{ $t('portal.quote.pay.loading') }}</p>
      </div>
    </div>
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
      if (this.quote.paid === false && this.quote.expired === false) {
        setTimeout(()=> {
          that.card = stripeservice.getCardToken(that.stripe, that.stripeConfig.token);
        }, 500)
      }
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
    send: function (value) {
      this.sending = true;
      var that = this
      stripeservice.sendCard(this.stripe, this.card).then(
          response => {
            var token = response.token.id;
            const hash = this.$route.params.hash;
            billingservice.portalQuotePay(hash, token).then(response => {
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