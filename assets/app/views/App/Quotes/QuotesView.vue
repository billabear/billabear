<template>
  <div>
    <h1 class="page-title mt-5 ml-5">{{ $t('app.quotes.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="p-5">
        <div class="alert-success mb-5" v-if="quote.paid">{{ $t('app.quotes.view.status.paid',  {date: $filters.moment(quote.paid_at, 'LLL')}) }}</div>
        <div class="grid grid-cols-2 gap-4">
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.quotes.view.customer.title') }}</h2>
            <div class="section-body">

              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.quotes.view.customer.email') }}</dt>
                  <dd>{{ quote.customer.email }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.customer.address.company_name') }}</dt>
                  <dd>{{ quote.customer.address.company_name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.customer.address.street_line_one') }}</dt>
                  <dd>{{ quote.customer.address.street_line_one }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.customer.address.street_line_two') }}</dt>
                  <dd>{{ quote.customer.address.street_line_two }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.customer.address.city') }}</dt>
                  <dd>{{ quote.customer.address.city }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.customer.address.region') }}</dt>
                  <dd>{{ quote.customer.address.region }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.customer.address.country') }}</dt>
                  <dd>{{ quote.customer.address.country }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.customer.address.post_code') }}</dt>
                  <dd>{{ quote.customer.address.post_code }}</dd>
                </div>
              </dl>
              <router-link :to="{name: 'app.customer.view', params: {id: quote.customer.id}}" class="btn--main">{{ $t('app.quotes.view.customer.more_info') }}</router-link>
            </div>
          </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.quotes.view.quote.title') }}</h2>
            <div class="section-body">
              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.quotes.view.quote.created_by') }}</dt>
                  <dd>{{ quote.created_by.display_name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.quote.created_at') }}</dt>
                  <dd>{{ quote.created_at }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.quote.expires_at') }}</dt>
                  <dd>{{ quote.expires_at }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.quotes.view.quote.pay_link') }}</dt>
                  <dd>{{ quote.pay_link }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
        <div class="mt-5">
          <h2 class="my-3">{{ $t('app.quotes.view.lines.title') }}</h2>

          <table class="list-table">
            <thead>
            <tr>
              <th>{{ $t('app.quotes.view.lines.description') }}</th>
              <th>{{ $t('app.quotes.view.lines.schedule') }}</th>
              <th>{{ $t('app.quotes.view.lines.tax_rate') }}</th>
              <th>{{ $t('app.quotes.view.lines.amount') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="line in quote.lines">
              <td>
                <span v-if="line.subscription_plan === null || line.subscription_plan === undefined">{{ line.description }}</span>
                <span v-else-if="line.seat_number">{{ line.seat_number }} x {{ line.subscription_plan.name }}</span>
                <span v-else>{{ line.subscription_plan.name }}</span>
              </td>
              <td v-if="line.price !== undefined && line.price !== null">{{ line.price.schedule }}</td>
              <td v-else>{{ $t('app.quotes.view.lines.one_off') }}</td>
              <td v-if="line.tax_rate !== null">{{ line.tax_rate }}</td>
              <td v-else>{{ $t('app.quotes.view.lines.tax_exempt') }}</td>
              <td>
                <Currency :amount="line.total" />
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="my-3 text-end">
          <div class="float-right text-end w-1/5">
            <h3 class="text-xl">{{ $t('app.quotes.view.total.title') }}</h3>

            <dl class="total-list">
              <div>
                <dt>{{ $t('app.quotes.view.total.tax_total') }}</dt>
                <dd>
                  <Currency :currency="quote.currency" :amount="quote.tax_total" />
                </dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.total.sub_total') }}</dt>
                <dd>
                  <Currency :currency="quote.currency" :amount="quote.sub_total" />
                </dd>
              </div>
              <div>
                <dt>{{ $t('app.quotes.view.total.total') }}</dt>
                <dd>
                  <Currency :currency="quote.currency" :amount="quote.total" />
                </dd>
              </div>
            </dl>
          </div>
        </div>

      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import Currency from "../../../components/app/Currency.vue";

export default {
  name: "QuotesRead",
  components: {Currency},
  data() {
    return {
      quote: {},
      ready: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/quotes/"+id+"/view").then(response => {
      this.quote = response.data.quote;
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