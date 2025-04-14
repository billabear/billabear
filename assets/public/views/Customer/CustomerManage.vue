<template>
  <div class="" v-if="ready">
    <div class="" v-if="!error">
      <div class="mx-auto lg:w-3/4 lg:border-l lg:border-r border-gray-400 h-full min-h-screen">
        <div class="bg-teal-500 p-5">
          <img src="/images/app-logo.png" class="" alt="BillaBear" />
        </div>
        <div class="p-5">
          <h1 class="text-4xl font-bold">{{ $t('portal.customer.manage.title') }}</h1>

          <div class="panel">
            <h2 class="text-3xl font-bold mb-3">{{ $t('portal.customer.manage.subscriptions.title') }}</h2>

            <ul>
              <li v-for="subscription in subscriptions" class="py-3">
                <div class="grid grid-cols-4">
                  <div class="col-span-3">
                    <h3 class="text-xl"><strong class="font-bold">{{ $t('portal.customer.manage.subscriptions.plan_name') }}:</strong> {{ subscription.plan.name }}</h3>
                    <div class="pr-2 inline"><strong>{{ $t('portal.customer.manage.subscriptions.status') }}:</strong> {{ subscription.status }}</div>
                    <div class="pr-2 inline"><strong>{{ $t('portal.customer.manage.subscriptions.next_billing_cycle') }}:</strong>  {{ $filters.moment(subscription.valid_until, 'llll') }}</div>
                  </div>
                  <div class="text-end">
                    <button class="btn--danger" @click="showCancel(subscription)" v-if="subscription.status != 'cancelled' && subscription.status != 'pending_cancel'">
                      {{ $t('portal.customer.manage.subscriptions.cancel') }}
                    </button>
                  </div>
                </div>
              </li>
              <li v-if="subscriptions.length === 0">
                <div class="text-center">
                  {{ $t('portal.customer.manage.subscriptions.no_subscriptions') }}
                </div>
              </li>
            </ul>
          </div>

          <div class="panel">
            <h2 class="text-3xl font-bold mb-3">{{ $t('portal.customer.manage.payment_methods.title') }}</h2>

            <ul v-if="customer.billing_type === 'invoice'">
              <li >
                <div class="text-center italic">
                  {{ $t('portal.customer.manage.payment_methods.invoiced_customer') }}
                </div>
              </li>
            </ul>
            <ul v-else>
              <li v-if="payment_methods.length === 0">
                <div class="text-center italic">
                  {{ $t('portal.customer.manage.payment_methods.no_payment_method') }}
                </div>
              </li>
              <li v-else v-for="method in payment_methods" class="py-3">

                <div class="grid grid-cols-4">
                  <div class="col-span-3">
                    {{ method.brand }} - **** **** **** {{ method.last_four }}
                  </div>
                  <div class="text-end">
                    <button class="btn--main" v-if="!method.default">
                      {{ $t('portal.customer.manage.payment_methods.make_default') }}
                    </button>
                  </div>
                </div>
              </li>
            </ul>
          </div>

          <div class="panel">
            <h2 class="text-3xl font-bold mb-3">{{ $t('portal.customer.manage.invoices.title') }}</h2>
            <ul>
              <li v-for="invoice in invoices" class="py-3">

                <div class="grid grid-cols-4">
                  <div class="col-span-3">
                    <h3 class="text-xl"><strong class="font-bold">{{ $t('portal.customer.manage.invoices.number') }}:</strong> {{ invoice.number }}</h3>
                    <div class="pr-2 inline">
                      <strong>{{ $t('portal.customer.manage.invoices.total') }}: </strong>
                      <Currency :amount="invoice.amount" :currency="invoice.currency" />
                    </div>
                    <div class="pr-2 inline">
                      <strong>{{ $t('portal.customer.manage.invoices.status') }}: </strong>
                      <span v-if="invoice.paid" class="bg-green-400 text-white font-bold p-1 rounded-lg">{{ $t('portal.customer.manage.invoices.paid') }}</span>
                      <span v-else class="bg-red-500 text-white font-bold p-1 rounded-lg">{{ $t('portal.customer.manage.invoices.outstanding') }}</span>
                    </div>
                    <div class="pr-2 inline" v-if="invoice.paid">
                      <strong>{{ $t('portal.customer.manage.invoices.paid_at') }}:</strong>  {{ $filters.moment(invoice.paid_at, 'llll') }}
                    </div>
                  </div>
                  <div class="text-end">
                    <SubmitButton :in-progress="sending_charge" button-class="btn-secondary mr-3" @click="chargeInvoice(invoice)" v-if="!invoice.paid">
                      {{ $t('portal.customer.manage.invoices.pay_now') }}
                    </SubmitButton>
                    <button class="btn--main" @click="downloadInvoice(invoice)">
                      {{ $t('portal.customer.manage.invoices.download') }}
                    </button>
                  </div>
                </div>
              </li>
              <li v-if="invoices.length === 0" class="text-center italic">
                {{ $t('portal.customer.manage.invoices.no_invoices') }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="flex justify-center items-center h-screen" v-else>
      <div class="text-center">
        <div class="text-center">
          <img src="/images/public-logo.png" class="w-80 block m-auto" alt="BillaBear" />
        </div>
        <p class="text-2xl font-bold block">
          {{ $t('portal.customer.manage.error_message') }}
        </p>
      </div>
    </div>
  </div>
  <div class="flex justify-center items-center h-screen" v-else>
    <img src="/images/public-logo.png" class="w-80 animate-fade-in-out" alt="BillaBear" />
  </div>
  <VueFinalModal v-model="modals.subscription_cancel"
                 class="flex justify-center items-center"
                 content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2 text-center">
    <CancelSubscription :subscription="active_subscription" :token="token"  @close-modal="modals.subscription_cancel = false;active_subscription.status = 'pending_cancel'" />
  </VueFinalModal>
  <VueFinalModal v-model="modals.error_message.show"
                 class="flex justify-center items-center"
                 content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2 text-center">
    <div class="text-center">
      <h3 class="mb-4 text-2xl font-semibold">{{ $t('portal.customer.manage.modal.error.title') }}</h3>
      <i class="fa-solid fa-circle-exclamation text-red-500 block text-5xl"></i>
      <p class="py-5">
        {{ $t('portal.customer.manage.modal.error.error_message', {message: modals.error_message.message}) }}
      </p>
    </div>
  </VueFinalModal>
</template>

<script>
import axios from "axios";
import {Button} from "flowbite-vue";
import {VueFinalModal} from "vue-final-modal";
import CancelSubscription from "./Components/CancelSubscription.vue";
import {DialogBackdrop} from "@headlessui/vue";
import fileDownload from "js-file-download";
import Currency from "../../../billabear/components/app/Currency.vue";

export default {
  name: "CustomerManage",
  components: {Currency, DialogBackdrop, CancelSubscription, VueFinalModal, Button},
  data(){
    return {
      token: null,
      ready: false,
      valid: false,
      customer: {},
      subscriptions: [],
      invoices: [],
      payment_methods: [],
      active_subscription: null,
      sending_charge: false,
      modals: {
        subscription_cancel: false,
        error_message: {
          show: false,
          message: null,
        }
      }
    }
  },
  methods: {
    downloadInvoice: function (invoice) {
      this.showError = false;
      axios.get('/public/customer/'+this.token+'/invoice/'+invoice.id+'/download', {  responseType: 'blob'}).then(response => {
        var fileDownload = require('js-file-download');
        const contentDisposition = response.headers['content-disposition'];
        let filename = 'invoice-'+invoice.number+'.pdf';
        if (contentDisposition) {const filenameRegex = /filename=(.*)/;
          const filenameMatch = filenameRegex.exec(contentDisposition);
          filename = filenameMatch[1];
        }
        fileDownload(response.data, filename);
        this.downloadInProgress = false;
      }).catch(error => {
        console.log(error)
        var that = this;
        let errorString = async function getString() {
          const str = await error.response.data.text();
          const errorString = JSON.parse(str);
          return str;
        }
        errorString();

        this.showError= true;
        this.downloadInProgress = false;
      })
    },
    showCancel: function (subscription) {
       this.active_subscription = subscription;
        this.modals.subscription_cancel = true;
    },
    chargeInvoice: function (invoice) {
      this.sending_charge = true;
      axios.post('/public/customer/'+this.token+'/invoice/'+invoice.id+'/charge', {  responseType: 'blob'}).then(response => {

          if (response.data.paid) {
            invoice.paid = true;
            invoice.paid_at = Date.now();
          } else {
            this.modals.error_message.show = true;
            this.modals.error_message.message = response.data.failure_reason;
          }
          this.sending_charge = false;

      }).catch(error => {

        this.modals.error_message.show = true;
        if (error.response !== undefined && error.response.data.reason !== undefined) {
          this.modals.error_message.message = error.response.data.failure_reason;
        } else {
          this.modals.error_message.message = 'unknown reason';
        }
          this.sending_charge = false;
      })
    }
  },
  mounted() {
    this.token= this.$route.params.token;
    axios.get("/public/customer/"+this.token+"/manage").then(response => {
      this.ready = true;
      this.error = false
      this.customer = response.data.customer;
      this.subscriptions = response.data.subscriptions;
      this.invoices = response.data.invoices;
      this.payment_methods = response.data.payment_methods;

    }).catch(error => {
      this.ready = true;
      this.error = true;
    })
  }
}
</script>

<style scoped>
.panel {
  @apply mt-5;
}
</style>