<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-3 text-end">
          <router-link :to="{name: 'app.customer.update'}" class="btn--main">{{ $t('app.customer.view.update') }}</router-link>
        </div>
        <div class="grid grid-cols-2 gap-3">
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
          <div class="grid grid-cols-2">
            <div><h2 class="mb-3">{{ $t('app.customer.view.subscriptions.title') }}</h2></div>
            <div class="text-end"><router-link :to="{name: 'app.subscription.create', params: {customerId: customer.id}}" class="btn--main">{{ $t('app.customer.view.subscriptions.add_new') }}</router-link></div>
          </div>



          <table class="list-table">
            <thead>
            <tr>
              <th>{{ $t('app.customer.view.subscriptions.list.plan_name') }}</th>
              <th>{{ $t('app.customer.view.subscriptions.list.status') }}</th>
              <th>{{ $t('app.customer.view.subscriptions.list.schedule') }}</th>
              <th>{{ $t('app.customer.view.subscriptions.list.created_at') }}</th>
              <th>{{ $t('app.customer.view.subscriptions.list.valid_until') }}</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="subscription in subscriptions" class="mt-5">
              <td>{{ subscription.plan.name }}</td>
              <td>{{ subscription.status }}</td>
              <td>{{ subscription.schedule }}</td>
              <td>{{ subscription.created_at }}</td>
              <td>{{ subscription.valid_until }}</td>
              <td><router-link :to="{name: 'app.subscription.view', params: {subscriptionId: subscription.id}}" class="list-btn">{{ $t('app.customer.view.subscriptions.list.view') }}</router-link></td>
            </tr>
            <tr v-if="subscriptions.length == 0">
              <td colspan="6" class="text-center">sdfds</td>
            </tr>
            </tbody>
          </table>
        </div>


        <div class="mt-3">
          <h2 class="mb-3">{{ $t('app.customer.view.payment_details.title') }}</h2>
          <table class="list-table">
            <thead>
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
                <td>
                  <button @click="defaultPayment(paymentDetail.id)" class="list-btn mr-2" v-if="!paymentDetail.default">{{$t('app.customer.view.payment_details.make_default') }}</button>
                  <button @click="deletePayment(paymentDetail.id)" class="list-btn" v-if="!paymentDetail.default">
                    <i class="fa-solid fa-trash"></i>
                    {{$t('app.customer.view.payment_details.delete') }}
                  </button>
                </td>
              </tr>
              <tr v-if="paymentDetails.length == 0">
                <td colspan="5" class="text-center">{{$t('app.customer.view.payment_details.no_payment_details') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
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
      subscriptions: [],
    }
  },
  methods: {
    deletePayment: function (id) {
      var customerId = this.$route.params.id
      axios.delete('/app/customer/'+customerId+'/payment-details/'+id).then(response => {
        for (var i = 0; i < this.paymentDetails.length; i++) {
          if (this.paymentDetails[i].id == id) {
            this.paymentDetails.splice(i, 1);
          }
        }
      })
    },
    defaultPayment: function (id) {
      var customerId = this.$route.params.id
      axios.post('/app/customer/'+customerId+'/payment-details/'+id+'/default').then(response => {
        for (var i = 0; i < this.paymentDetails.length; i++) {
          if (this.paymentDetails[i].id == id) {
            this.paymentDetails[i].default = true;
          } else {
            this.paymentDetails[i].default = false;
          }
        }
      })
    }
  },
  mounted() {
    var customerId = this.$route.params.id
    axios.get('/app/customer/'+customerId).then(response => {
      this.customer = response.data.customer;
      this.paymentDetails = response.data.payment_details;
      this.subscriptions = response.data.subscriptions;
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