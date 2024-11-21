<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.invoices.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="">
        <div class="mb-3">
          <div class="alert-success" v-if="invoice.paid">{{ $t('app.invoices.view.status.paid',  {date: $filters.moment(invoice.paid_at, 'LLL')}) }}</div>
          <div class="alert-error" v-else>{{ $t('app.invoices.view.status.outstanding') }}</div>
        </div>

        <div class="text-end mb-3">
          <SubmitButton :in-progress="chargingCard" @click="chargeCard" button-class=" btn--main" v-if="invoice.customer.billing_type == 'card' && invoice.paid == false">{{ $t('app.invoices.view.actions.charge_card') }}</SubmitButton>
          <SubmitButton :in-progress="markingAsPaid" @click="markAsPaid" button-class="ml-3 btn--secondary" v-if="invoice.paid === false">{{ $t('app.invoices.view.actions.mark_as_paid') }}</SubmitButton>
          <button class="ml-3 btn--main" target="_blank" @click="showDownload = true">{{ $t('app.invoices.view.download') }}</button>
        </div>

        <div class="card-body">
            <h2 class="section-header">{{ $t('app.invoices.view.main.title') }}</h2>
            <div class="section-body">

              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.invoices.view.main.created_at') }}</dt>
                  <dd>{{ $filters.moment(invoice.created_at, 'llll') }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.main.pay_link') }}</dt>
                  <dd>{{ invoice.pay_link }}</dd>
                </div>
                <div v-if="invoice.due_date">
                  <dt>{{ $t('app.invoices.view.main.due_date') }}</dt>
                  <dd>{{ $filters.moment(invoice.due_date, 'llll') }}</dd>
                </div>
              </dl>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-4">
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.invoices.view.customer.title') }}</h2>
            <div class="section-body">

              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.invoices.view.customer.email') }}</dt>
                  <dd>{{ invoice.customer.email }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.customer.address.company_name') }}</dt>
                  <dd>{{ invoice.payee_address.company_name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.customer.address.street_line_one') }}</dt>
                  <dd>{{ invoice.payee_address.street_line_one }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.customer.address.street_line_two') }}</dt>
                  <dd>{{ invoice.payee_address.street_line_two }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.customer.address.city') }}</dt>
                  <dd>{{ invoice.payee_address.city }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.customer.address.region') }}</dt>
                  <dd>{{ invoice.payee_address.region }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.customer.address.country') }}</dt>
                  <dd>{{ invoice.payee_address.country }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.customer.address.post_code') }}</dt>
                  <dd>{{ invoice.payee_address.post_code }}</dd>
                </div>
              </dl>
              <router-link :to="{name: 'app.customer.view', params: {id: invoice.customer.id}}" class="btn--container">{{ $t('app.invoices.view.customer.more_info') }}</router-link>
            </div>
          </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.invoices.view.biller.title') }}</h2>
            <div class="section-body">

              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.invoices.view.biller.address.company_name') }}</dt>
                  <dd>{{ invoice.biller_address.company_name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.biller.address.street_line_one') }}</dt>
                  <dd>{{ invoice.biller_address.street_line_one }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.biller.address.street_line_two') }}</dt>
                  <dd>{{ invoice.biller_address.street_line_two }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.biller.address.city') }}</dt>
                  <dd>{{ invoice.biller_address.city }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.biller.address.region') }}</dt>
                  <dd>{{ invoice.biller_address.region }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.biller.address.country') }}</dt>
                  <dd>{{ invoice.biller_address.country }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.invoices.view.biller.address.post_code') }}</dt>
                  <dd>{{ invoice.biller_address.post_code }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>

        <h2 class="my-3  dark:text-gray-300">{{ $t('app.invoices.view.lines.title') }}</h2>
        <div class="card-body">

          <table class="list-table">
            <thead>
              <tr>
                <th>{{ $t('app.invoices.view.lines.description') }}</th>
                <th>{{ $t('app.invoices.view.lines.tax_rate') }}</th>
                <th>{{ $t('app.invoices.view.lines.amount') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="line in invoice.lines">
                <td>{{line.description }}</td>
                <td v-if="line.tax_rate !== null">{{ line.tax_rate }}</td>
                <td v-else>{{ $t('app.invoices.view.lines.tax_exempt') }}</td>
                <td>
                  <Currency :amount="line.total" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-3 text-end">
          <div class="w-full text-end">
            <h3 class="text-xl dark:text-gray-500">{{ $t('app.invoices.view.total.title') }}</h3>

            <dl class="total-list">
              <div>
                <dt>{{ $t('app.invoices.view.total.tax_total') }}</dt>
                <dd>
                  <Currency :currency="invoice.currency" :amount="invoice.tax_total" />
                </dd>
              </div>
              <div>
                <dt>{{ $t('app.invoices.view.total.sub_total') }}</dt>
                <dd>
                  <Currency :currency="invoice.currency" :amount="invoice.sub_total" />
                </dd>
              </div>
              <div>
                <dt>{{ $t('app.invoices.view.total.total') }}</dt>
                <dd>
                  <Currency :currency="invoice.currency" :amount="invoice.total" />
                </dd>
              </div>
            </dl>
          </div>
        </div>
        <div class="mt-3 card-body relative">
          <h2 class="text-2xl mb-3">{{ $t('app.invoices.view.invoice_delivery.title') }}</h2>
          <table class="list-table">
            <thead>
              <tr>
                <th>{{ $t('app.invoices.view.invoice_delivery.method') }}</th>
                <th>{{ $t('app.invoices.view.invoice_delivery.detail') }}</th>
                <th>{{ $t('app.invoices.view.invoice_delivery.status') }}</th>
                <th>{{ $t('app.invoices.view.invoice_delivery.created_at') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="invoiceDelivery in invoice_deliveries">
                <td>{{ invoiceDelivery.invoice_delivery_settings.type }}</td>
                <td v-if="invoiceDelivery.invoice_delivery_settings.type === 'email'">{{ invoiceDelivery.invoice_delivery_settings.email }}</td>
                <td v-if="invoiceDelivery.invoice_delivery_settings.type === 'sftp'">{{ invoiceDelivery.invoice_delivery_settings.sftp_host }}</td>
                <td v-if="invoiceDelivery.invoice_delivery_settings.type === 'webhook'">{{ invoiceDelivery.invoice_delivery_settings.webhook_url }}</td>
                <td>{{ invoiceDelivery.status }}</td>
                <td>{{ $filters.moment(invoiceDelivery.created_at, 'llll') }}</td>
              </tr>
              <tr>
                <td colspan="5" class="text-center">{{ $t('app.invoices.view.invoice_delivery.no_invoice_deliveries') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </LoadingScreen>

    <VueFinalModal
        v-model="failed.modelValue"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      {{ $t('app.invoices.view.payment_failed.message') }}
    </VueFinalModal>

    <VueFinalModal
        v-model="success.modelValue"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      {{ $t('app.invoices.view.payment_succeeded.message') }}
    </VueFinalModal>

    <VueFinalModal
        v-model="showDownload"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2">
      <DownloadInvoice :invoice="invoice" />
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import Currency from "../../../components/app/Currency.vue";
import {VueFinalModal} from "vue-final-modal";
import DownloadInvoice from "../../../components/app/Invoice/Modals/DownloadInvoice.vue";
import {Button} from "flowbite-vue";

export default {
  name: "InvoiceView",
  components: {Button, DownloadInvoice, VueFinalModal, Currency},
  data() {
    return {
      invoice: {},
      invoice_deliveries: [],
      ready: false,
      chargingCard: false,
      markingAsPaid: false,
      failed: {
        modelValue: false,
      },
      success: {
        modelValue: false,
      },
      showDownload: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/invoice/"+id+"/view").then(response => {
      this.invoice = response.data.invoice;
      this.invoice_deliveries = response.data.invoice_deliveries;
      this.ready = true;
    })
  },
  methods: {
    markAsPaid: function () {
        this.markingAsPaid = true;

      this.charging_invoice = true;
      axios.post('/app/invoice/'+this.invoice.id+'/paid').then(response => {
        this.invoice.paid = true
        this.invoice.paid_at = Date.now();
        this.markingAsPaid = false;
      }).catch(error => {
        console.log(error)
        this.failed.modelValue = true;
        this.markingAsPaid = false;
      })
    },
    chargeCard: function () {
      this.chargingCard = true;

      axios.post('/app/invoice/'+this.invoice.id+'/charge').then(response => {
        this.invoice.paid = response.data.paid;
        if (this.invoice.paid === false) {
          this.failed.modelValue = true;
        } else {
          this.invoice.paid_at = Date.now();
          this.success.modelValue = true;
        }
        this.chargingCard = false;
      }).catch(error => {
        console.log(error)
        this.failed.modelValue = true;
        this.chargingCard = false;
      })
    }
  }
}
</script>

<style scoped>
.total-list {
}

.total-list div:nth-child(even) {
  @apply  py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0;
}

.total-list div:nth-child(odd) {
  @apply  py-1 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0;
}

.total-list dt {
  @apply text-sm font-medium text-gray-500;
}

.total-list dd {
  @apply mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0;
}
</style>
