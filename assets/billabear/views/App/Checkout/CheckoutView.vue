<template>
  <div>
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.checkout.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="p-5">
        <div class="grid grid-cols-2 gap-4">
          <div v-if="checkout.customer">
            <h2 class="section-header">{{ $t('app.checkout.view.customer.title') }}</h2>
            <div class="section-body">

              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.checkout.view.customer.email') }}</dt>
                  <dd>{{ checkout.customer.email }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.customer.address.company_name') }}</dt>
                  <dd>{{ checkout.customer.address.company_name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.customer.address.street_line_one') }}</dt>
                  <dd>{{ checkout.customer.address.street_line_one }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.customer.address.street_line_two') }}</dt>
                  <dd>{{ checkout.customer.address.street_line_two }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.customer.address.city') }}</dt>
                  <dd>{{ checkout.customer.address.city }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.customer.address.region') }}</dt>
                  <dd>{{ checkout.customer.address.region }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.customer.address.country') }}</dt>
                  <dd>{{ checkout.customer.address.country }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.customer.address.post_code') }}</dt>
                  <dd>{{ checkout.customer.address.post_code }}</dd>
                </div>
              </dl>
              <router-link :to="{name: 'app.customer.view', params: {id: checkout.customer.id}}" class="btn--main">{{ $t('app.checkout.view.customer.more_info') }}</router-link>
            </div>
          </div>
          <div>
            <h2 class="section-header">{{ $t('app.checkout.view.checkout.title') }}</h2>
            <div class="section-body">
              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.checkout.view.checkout.name') }}</dt>
                  <dd>{{ checkout.name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.checkout.created_by') }}</dt>
                  <dd>{{ checkout.created_by.display_name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.checkout.created_at') }}</dt>
                  <dd>{{ checkout.created_at }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.checkout.expires_at') }}</dt>
                  <dd>{{ checkout.expires_at }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.checkout.view.checkout.pay_link') }}</dt>
                  <dd>{{ checkout.pay_link }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
        <div class="">
          <h2 class="my-3">{{ $t('app.checkout.view.lines.title') }}</h2>

          <table class="list-table">
            <thead>
              <tr>
                <th>{{ $t('app.checkout.view.lines.description') }}</th>
                <th>{{ $t('app.checkout.view.lines.schedule') }}</th>
                <th>{{ $t('app.checkout.view.lines.tax_rate') }}</th>
                <th>{{ $t('app.checkout.view.lines.amount') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="line in checkout.lines">
                <td>
                  <span v-if="line.subscription_plan === null || line.subscription_plan === undefined">{{ line.description }}</span>
                  <span v-else-if="line.seat_number">{{ line.seat_number }} x {{ line.subscription_plan.name }}</span>
                  <span v-else>{{ line.subscription_plan.name }}</span>
                </td>
                <td v-if="line.price !== undefined && line.price !== null">{{ line.price.schedule }}</td>
                <td v-else>{{ $t('app.quotes.view.lines.one_off') }}</td>
                <td v-if="line.tax_rate !== null">{{ line.tax_rate }}</td>
                <td v-else>{{ $t('app.checkout.view.lines.tax_exempt') }}</td>
                <td>
                  <Currency :amount="line.total" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="my-3 text-end">
          <div class="float-right text-end w-1/5">
            <h3 class="text-xl">{{ $t('app.checkout.view.total.title') }}</h3>

            <dl class="total-list">
              <div>
                <dt>{{ $t('app.checkout.view.total.tax_total') }}</dt>
                <dd>
                  <Currency :currency="checkout.currency" :amount="checkout.tax_total" />
                </dd>
              </div>
              <div>
                <dt>{{ $t('app.checkout.view.total.sub_total') }}</dt>
                <dd>
                  <Currency :currency="checkout.currency" :amount="checkout.sub_total" />
                </dd>
              </div>
              <div>
                <dt>{{ $t('app.checkout.view.total.total') }}</dt>
                <dd>
                  <Currency :currency="checkout.currency" :amount="checkout.total" />
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
  name: "CheckoutRead",
  components: {Currency},
  data() {
    return {
      checkout: {
        customer: {}
      },
      ready: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/checkout/"+id+"/view").then(response => {
      this.checkout = response.data.checkout;
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