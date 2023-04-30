<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-3 text-end">
          <button class="btn--danger mr-3" v-if="customer.status == 'disabled'" @click="enableCustomer">{{ $t('app.customer.view.enable') }}</button>
          <button class="btn--danger mr-3" v-else @click="disableCustomer">{{ $t('app.customer.view.disable') }}</button>
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
                <dt>{{ $t('app.customer.view.main.locale') }}</dt>
                <dd>{{ customer.locale }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.customer.view.main.brand') }}</dt>
                <dd>{{ customer.brand }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.customer.view.main.status') }}</dt>
                <dd>{{ customer.status }}</dd>
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
              <td colspan="6" class="text-center">{{ $t('app.customer.view.subscriptions.no_subscriptions') }}</td>
            </tr>
            </tbody>
          </table>
        </div>


          <div class="mt-3">
            <div class="grid grid-cols-2">
              <div><h2 class="mb-3">{{ $t('app.customer.view.payment_details.title') }}</h2></div>
              <div><router-link class="btn--main" :to="{name: 'app.customer.payment_details.add', params: {customerId: customer.id}}">{{ $t('app.customer.view.payment_details.add_new') }}</router-link></div>
            </div>

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

          <div class="mt-3">
            <h2 class="mb-5">{{ $t('app.customer.view.limits.title') }}</h2>

            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.customer.view.limits.list.feature') }}</th>
                <th>{{ $t('app.customer.view.limits.list.limit') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(limit, key) in limits.limits">
                <td>{{ key }}</td>
                <td>{{ limit }}</td>
              </tr>
              <tr v-if="Object.keys(limits.limits).length == 0">
                <td colspan="4" class="text-center">{{ $t('app.customer.view.limits.no_limits') }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            <h2 class="mb-5">{{ $t('app.customer.view.features.title') }}</h2>

            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.customer.view.features.list.feature') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="feature in limits.features">
                <td>{{ feature }}</td>
              </tr>
              <tr v-if="limits.features.length == 0">
                <td colspan="4" class="text-center">{{ $t('app.customer.view.features.no_features') }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            <h2 class="mb-3">{{ $t('app.customer.view.payments.title') }}</h2>
            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.customer.view.payments.list.amount') }}</th>
                <th>{{ $t('app.customer.view.payments.list.currency') }}</th>
                <th>{{ $t('app.customer.view.payments.list.status') }}</th>
                <th>{{ $t('app.customer.view.payments.list.created_at') }}</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="payment in payments" class="mt-5">
                <td>{{ payment.amount }}</td>
                <td>{{ payment.currency }}</td>
                <td>{{ payment.status }}</td>
                <td>{{ $filters.moment(payment.created_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}</td>
                <td><router-link :to="{name: 'app.payment.view', params: {id: payment.id}}" class="list-btn">View</router-link></td>
              </tr>
              <tr v-if="payments.length == 0">
                <td colspan="5" class="text-center">{{$t('app.customer.view.payments.no_payments') }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            <h2 class="mb-5">{{ $t('app.customer.view.refunds.title') }}</h2>

            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.customer.view.refunds.list.amount') }}</th>
                <th>{{ $t('app.customer.view.refunds.list.currency') }}</th>
                <th>{{ $t('app.customer.view.refunds.list.created_by') }}</th>
                <th>{{ $t('app.customer.view.refunds.list.created_at') }}</th>
                <td></td>
              </tr>
              </thead>
              <tbody>
              <tr v-for="refund in refunds">
                <td>{{ refund.amount }}</td>
                <td>{{ refund.currency }}</td>
                <td v-if="refund.billing_admin != null">{{ refund.billing_admin.display_name }}</td>
                <td v-else>API</td>
                <td>{{ $filters.moment(refund.created_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}</td>
              </tr>
              <tr v-if="refunds.length == 0">
                <td colspan="4" class="text-center">{{ $t('app.customer.view.refunds.no_refunds') }}</td>
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
      payments: [],
      refunds: [],
      subscriptions: [],
      limits: {}
    }
  },
  methods: {
    disableCustomer: function() {
      var customerId = this.$route.params.id;
      axios.post('/app/customer/'+customerId+'/disable').then(response => {
        this.customer.status = 'disabled';
      })
    },
    enableCustomer: function()  {
      var customerId = this.$route.params.id;
      axios.post('/app/customer/'+customerId+'/enable').then(response => {
        this.customer.status = 'active';
      })
    },
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
      this.payments = response.data.payments;
      this.refunds = response.data.refunds;
      this.limits = response.data.limits;
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