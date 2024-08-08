<template>
  <div>
    <div class="grid grid-cols-2">
      <div>
        <h2 class="section-header">{{ $t('app.customer.view.invoice_delivery.title') }}</h2>
      </div>
      <div class="text-end">
        <router-link class="btn--main" :to="{name: 'app.customer.invoice_delivery.add', params: {customerId: this.customer.id}}">{{ $t('app.customer.view.invoice_delivery.add_new') }}</router-link>
      </div>
    </div>
    <div class="">

      <table class="list-table">
        <thead>
        <tr>
          <th>{{ $t('app.customer.view.invoice_delivery.list.method') }}</th>
          <th>{{ $t('app.customer.view.invoice_delivery.list.format') }}</th>
          <th>{{ $t('app.customer.view.invoice_delivery.list.detail') }}</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="invoiceDeliveryMethod in invoiceDeliveryMethods">
          <td>{{ invoiceDeliveryMethod.type }}</td>
          <td>{{ invoiceDeliveryMethod.format }}</td>
          <td v-if="invoiceDeliveryMethod.type === 'webhook'">
            {{ invoiceDeliveryMethod.webhook_url }}
          </td>
          <td v-if="invoiceDeliveryMethod.type === 'sftp'">
            {{ invoiceDeliveryMethod.sftp_host }}
          </td>
          <td v-if="invoiceDeliveryMethod.type === 'email'">
            {{ invoiceDeliveryMethod.email }}
          </td>
          <td><RouterLink class="btn--main" :to="{name: 'app.customer.invoice_delivery.view', params: {customerId: this.customer.id, invoiceDeliveryId: invoiceDeliveryMethod.id}}">{{ $t('app.customer.view.invoice_delivery.list.view') }}</RouterLink></td>
        </tr>
        <tr v-if="invoiceDeliveryMethods.length == 0">
          <td colspan="5" class="text-center">{{ $t('app.customer.view.invoice_delivery.no_delivery_methods') }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "CustomerInvoiceDelivery",
  props: {
    invoiceResult: Object,
    customer: Object,
  },
  data() {
    return {
      fetechedResult: null,
    }
  },
  computed: {
    invoiceDeliveryMethods: function () {
      if (this.fetechedResult === null) {
        return this.invoiceResult.data;
      }
      return this.fetechedResult.data;
    },
  }
}
</script>

<style scoped>

</style>
