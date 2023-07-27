<template>
  <div>
    <h1 class="page-title mb-5">{{ $t('app.invoices.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="grid grid-cols-2 gap-4">
        <div>
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
            <router-link :to="{name: 'app.customer.view', params: {id: invoice.customer.id}}" class="btn--main">{{ $t('app.invoices.view.customer.more_info') }}</router-link>
          </div>
        </div>
        <div>
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
      <div class="">
        <h2 class="my-3">{{ $t('app.invoices.view.lines.title') }}</h2>

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
              <td>{{ invoice.total }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="my-3 text-end">
        <div class="float-right text-end w-1/5">
          <h3 class="text-xl">{{ $t('app.invoices.view.total.title') }}</h3>

          <dl class="total-list">
            <div>
              <dt>{{ $t('app.invoices.view.total.tax_total') }}</dt>
              <dd>{{ invoice.currency }} {{ invoice.tax_total }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.invoices.view.total.sub_total') }}</dt>
              <dd>{{ invoice.currency }} {{ invoice.sub_total }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.invoices.view.total.total') }}</dt>
              <dd>{{ invoice.currency }} {{ invoice.total }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "InvoiceView",
  data() {
    return {
      invoice: {},
      ready: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/invoice/"+id+"/view").then(response => {
      this.invoice = response.data.invoice;
      this.ready = true;
    })
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