<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-3 text-end">
          <router-link :to="{name: 'app.customer.update'}" class="btn--main">{{ $t('app.customer.view.update') }}</router-link>
        </div>
        <div class="mt-5">
          <h2 class="mb-3">{{ $t('app.customer.view.main.title') }}</h2>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.customer.view.main.email') }}</dt>
              <dd>{{ customer.email }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.customer.view.main.reference') }}</dt>
              <dd>{{ customer.reference }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.customer.view.main.external_reference') }}</dt>
              <dd>
                <a v-if="customer.payment_provider_details_url" target="_blank" :href="customer.payment_provider_details_url">{{ customer.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ customer.external_reference }}</span>
              </dd>
            </div>
          </dl>

        </div>
        <div class="mt-3">
          <h2 class="mb-3">{{ $t('app.customer.view.address.title') }}</h2>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.customer.view.address.street_line_one') }}</dt>
              <dd>{{ customer.address.street_line_one }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.customer.view.address.street_line_two') }}</dt>
              <dd>{{ customer.address.street_line_two }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.customer.view.address.city') }}</dt>
              <dd>{{ customer.address.city }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.customer.view.address.region') }}</dt>
              <dd>{{ customer.address.region }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.customer.view.address.country') }}</dt>
              <dd>{{ customer.address.country }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.customer.view.address.post_code') }}</dt>
              <dd>{{ customer.address.post_code }}</dd>
            </div>
          </dl>
        </div>
        <div class="mt-3">
          <h2 class="mb-3">{{ $t('app.customer.view.payment_details.title') }}</h2>
          <table class="list-table">
            <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
              <th>{{ $t('app.customer.view.payment_details.list.last_four') }}</th>
              <th>{{ $t('app.customer.view.payment_details.list.expiry_month') }}</th>
              <th>{{ $t('app.customer.view.payment_details.list.expiry_year') }}</th>
              <th>{{ $t('app.customer.view.payment_details.list.default') }}</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="paymentDetail in paymentDetails" class="mt-5">
              <td>{{ paymentDetail.last_four }}</td>
              <td>{{ paymentDetail.expiry_month }}</td>
              <td>{{ paymentDetail.expiry_year }}</td>
              <td>{{ paymentDetail.default }}</td>
              <td></td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
              <th>{{ $t('app.customer.view.payment_details.list.last_four') }}</th>
              <th>{{ $t('app.customer.view.payment_details.list.expiry_month') }}</th>
              <th>{{ $t('app.customer.view.payment_details.list.expiry_year') }}</th>
              <th>{{ $t('app.customer.view.payment_details.list.default') }}</th>
              <th></th>
            </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerView",
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      customer: {
      },
      paymentDetails: [],
    }
  },
  mounted() {
    var customerId = this.$route.params.id
    axios.get('/app/customer/'+customerId).then(response => {
      this.customer = response.data.customer;
      this.paymentDetails = response.data.payment_details;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
          this.errorMessage = this.$t('app.customer.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.customer.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>