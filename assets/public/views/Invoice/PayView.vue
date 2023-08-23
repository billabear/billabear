<template>
  <div class="mt-5">
    <div class="w-full mb-5 mt-5">
      <img src="/images/app-logo.png" alt="" class="" width="175" />
    </div>
    <h1 class="text-xl6 mb-5">{{ $t('portal.invoice.pay.title') }}</h1>
    <div v-if="ready">
      <div class="w-50 text-end mb-5">
        <strong>{{ $t('portal.invoice.pay.general.invoice_number') }}:</strong> {{ invoice.number }}
        <br /><strong>{{ $t('portal.invoice.pay.general.issued_at') }}:</strong> {{ invoice.created_at }}
      </div>
      <div class="grid grid-cols-2">
        <div class="w-50">
          <h3 class="mb-5 font-extrabold">{{ $t('portal.invoice.pay.biller_details.title') }}</h3>
          {{ invoice.biller_address.company_name }} <br v-if="invoice.biller_address.company_name" />
          {{ invoice.biller_address.street_line_one }}<br  v-if="invoice.biller_address.street_line_one" />
          {{ invoice.biller_address.street_line_two }}<br v-if="invoice.biller_address.street_line_two" />
          {{ invoice.biller_address.city }}<br v-if="invoice.biller_address.city" />
          {{ invoice.biller_address.region }}<br v-if="invoice.biller_address.region" />
          {{ invoice.biller_address.postcode }}<br v-if="invoice.biller_address.postcode" />
        </div>

        <div class="w-50 text-end">
          <h3 class="mb-5 font-extrabold">{{ $t('portal.invoice.pay.payee_details.title') }}</h3>
          {{ invoice.email_address }} <br v-if="invoice.email_address" />
          {{ invoice.payee_address.company_name }} <br v-if="invoice.payee_address.company_name" />
          {{ invoice.payee_address.street_line_one }}<br  v-if="invoice.payee_address.street_line_one" />
          {{ invoice.payee_address.street_line_two }}<br v-if="invoice.payee_address.street_line_two" />
          {{ invoice.payee_address.city }}<br v-if="invoice.payee_address.city" />
          {{ invoice.payee_address.region }}<br v-if="invoice.payee_address.region" />
          {{ invoice.payee_address.postcode }}<br v-if="invoice.payee_address.postcode" />
        </div>
      </div>

      <div class="my-5">
        <table class="table w-full">
          <thead>
            <tr>
              <th class="w-90">{{ $t('portal.invoice.pay.lines.description') }}</th>
              <th>{{ $t('portal.invoice.pay.lines.tax_rate') }}</th>
              <th>{{ $t('portal.invoice.pay.lines.tax_total') }}</th>
              <th>{{ $t('portal.invoice.pay.lines.total') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="line in invoice.lines">
              <td>{{ line.description }}</td>
              <td class="text-center">{{ line.tax_rate }}</td>
              <td class="text-center">{{ displayCurrency(line.tax_total) }}</td>
              <td class="text-center">{{ displayCurrency(line.total) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="my-5 text-end">
        <strong>{{ $t('portal.invoice.pay.totals.total') }}</strong> {{ displayCurrency(invoice.amount) }}
      </div>

      <div v-if="not_found">
        {{ $t('portal.invoice.pay.not_found') }}
      </div>
      <div v-else-if="general_error">
        {{ $t('portal.invoice.pay.general_error') }}
      </div>
      <div v-else-if="invoice.paid" class="text-center font-extrabold italic">
        {{ $t('portal.invoice.pay.payment.already_paid')}}
      </div>
      <div v-else>
       <form @submit.prevent="send" :disabled="sending">

         <div class="w-1/2 m-auto p-5">
           <h2>{{ $t('portal.invoice.pay.payment_details.title') }}</h2>
          <div id="cardInput" class="my-5"></div>
          <div id="cardError"></div>

         </div>
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