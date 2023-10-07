<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.customer.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
          <div class="m-3 text-end">
            <button class="btn--danger mr-3" v-if="customer.status == 'disabled'" @click="enableCustomer">{{ $t('app.customer.view.enable') }}</button>
            <button class="btn--danger mr-3" v-else @click="disableCustomer">{{ $t('app.customer.view.disable') }}</button>
            <router-link :to="{name: 'app.customer.update'}" class="btn--main">{{ $t('app.customer.view.update') }}</router-link>
          </div>
        </RoleOnlyView>

        <div class="grid grid-cols-2 gap-3 p-5">
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.customer.view.main.title') }}</h2>
            <div class="section-body">
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
                  <dt>{{ $t('app.customer.view.main.billing_type') }}</dt>
                  <dd>{{ customer.billing_type }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.customer.view.main.type') }}</dt>
                  <dd>{{ customer.type }}</dd>
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
                  <dt>{{ $t('app.customer.view.main.tax_number') }}</dt>
                  <dd>{{ customer.tax_number }}</dd>
                </div>
                <div v-if="customer.digital_tax_rate">
                  <dt>{{ $t('app.customer.view.main.digital_tax_rate') }}</dt>
                  <dd>{{ customer.digital_tax_rate }}</dd>
                </div>
                <div v-if="customer.standard_tax_rate">
                  <dt>{{ $t('app.customer.view.main.standard_tax_rate') }}</dt>
                  <dd>{{ customer.standard_tax_rate }}</dd>
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
          </div>
          <div class=" card-body">
            <h2 class="section-header">{{ $t('app.customer.view.address.title') }}</h2>
            <div class="section-body">

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
          </div>

        <div class="card-body">
          <div class="grid grid-cols-2">
            <div><h2  class="section-header">{{ $t('app.customer.view.subscriptions.title') }}</h2></div>
            <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
              <div class="text-end"><router-link :to="{name: 'app.subscription.create', params: {customerId: customer.id}}" class="btn--main">{{ $t('app.customer.view.subscriptions.add_new') }}</router-link></div>

            </RoleOnlyView>
          </div>

          <div class="mt-2">

            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.customer.view.subscriptions.list.plan_name') }}</th>
                <th>{{ $t('app.customer.view.subscriptions.list.status') }}</th>
                <th>{{ $t('app.customer.view.subscriptions.list.schedule') }}</th>
                <th>{{ $t('app.customer.view.subscriptions.list.valid_until') }}</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="subscription in subscriptions" class="mt-5">
                <td v-if="subscription.plan !== undefined && subscription.plan !== null">{{ subscription.plan.name }}</td>
                <td v-else>N/A</td>
                <td>{{ subscription.status }}</td>
                <td>{{ subscription.schedule }}</td>
                <td>{{ $filters.moment(subscription.valid_until, "LLL") }}</td>
                <td><router-link :to="{name: 'app.subscription.view', params: {subscriptionId: subscription.id}}" class="list-btn">{{ $t('app.customer.view.subscriptions.list.view') }}</router-link></td>
              </tr>
              <tr v-if="subscriptions.length == 0">
                <td colspan="6" class="text-center">{{ $t('app.customer.view.subscriptions.no_subscriptions') }}</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>


          <div class="card-body">
            <div class="grid grid-cols-2">
              <div><h2  class="section-header">{{ $t('app.customer.view.payment_details.title') }}</h2></div>
              <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
                <div><router-link class="btn--main" :to="{name: 'app.customer.payment_details.add', params: {customerId: customer.id}}">{{ $t('app.customer.view.payment_details.add_new') }}</router-link></div>
              </RoleOnlyView>
            </div>

            <div class="mt-2">

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
                    <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
                      <button @click="defaultPayment(paymentDetail.id)" class="list-btn mr-2" v-if="!paymentDetail.default">{{$t('app.customer.view.payment_details.make_default') }}</button>
                      <button @click="deletePayment(paymentDetail.id)" class="list-btn" v-if="!paymentDetail.default">
                        <i class="fa-solid fa-trash"></i>
                        {{$t('app.customer.view.payment_details.delete') }}
                      </button>
                    </RoleOnlyView>
                  </td>
                </tr>
                <tr v-if="paymentDetails.length == 0">
                  <td colspan="5" class="text-center">{{$t('app.customer.view.payment_details.no_payment_details') }}</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-body">
            <h2 class="section-header">{{ $t('app.customer.view.limits.title') }}</h2>
            <div class="">

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
          </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.customer.view.features.title') }}</h2>
            <div class="">

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
          </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.customer.view.payments.title') }}</h2>
            <div class="">
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
                <td>{{ currency(payment.amount) }}</td>
                <td>{{ payment.currency }}</td>
                <td>{{ payment.status }}</td>
                <td>{{ $filters.moment(payment.created_at, "LLL") || "unknown" }}</td>
                <td><router-link :to="{name: 'app.payment.view', params: {id: payment.id}}" class="list-btn">View</router-link></td>
              </tr>
              <tr v-if="payments.length == 0">
                <td colspan="5" class="text-center">{{$t('app.customer.view.payments.no_payments') }}</td>
              </tr>
              </tbody>
            </table></div>
          </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.customer.view.refunds.title') }}</h2>
            <div class="">
            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.customer.view.refunds.list.amount') }}</th>
                <th>{{ $t('app.customer.view.refunds.list.currency') }}</th>
                <th>{{ $t('app.customer.view.refunds.list.created_by') }}</th>
                <th>{{ $t('app.customer.view.refunds.list.created_at') }}</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="refund in refunds">
                <td>{{ currency(refund.amount) }}</td>
                <td>{{ refund.currency }}</td>
                <td v-if="refund.billing_admin != null">{{ refund.billing_admin.display_name }}</td>
                <td v-else>API</td>
                <td>{{ $filters.moment(refund.created_at, "LLL") || "unknown" }}</td>
              </tr>
              <tr v-if="refunds.length == 0">
                <td colspan="5" class="text-center">{{ $t('app.customer.view.refunds.no_refunds') }}</td>
              </tr>
              </tbody>
            </table></div>
          </div>
          <div class="card-body">
            <div class="grid grid-cols-2">
              <div>
                <h2 class="section-header">{{ $t('app.customer.view.credit.title') }}</h2>
              </div>
              <div class="text-end">
                <router-link :to="{name: 'app.customer.credit.add', params: {customerId: customer.id}}" class="btn--main">{{ $t('app.customer.view.credit.add_button') }}</router-link>
              </div>
            </div>
            <div class="">

              <table class="list-table">
                <thead>
                <tr>
                  <th>{{ $t('app.customer.view.credit.list.amount') }}</th>
                  <th>{{ $t('app.customer.view.credit.list.currency') }}</th>
                  <th>{{ $t('app.customer.view.credit.list.created_by') }}</th>
                  <th>{{ $t('app.customer.view.credit.list.created_at') }}</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="creditAdjustment in credit">
                  <td>{{ currency(creditAdjustment.amount) }}</td>
                  <td>{{ creditAdjustment.currency }}</td>
                  <td v-if="creditAdjustment.billing_admin != null">{{ creditAdjustment.billing_admin.display_name }}</td>
                  <td v-else>n/a</td>
                  <td>{{ $filters.moment(creditAdjustment.created_at, "LLL") || "unknown" }}</td>
                </tr>
                <tr v-if="credit.length == 0">
                  <td colspan="5" class="text-center">{{ $t('app.customer.view.credit.no_credit') }}</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-body">
            <div class="grid grid-cols-2">
              <div>
                <h2 class="section-header">{{ $t('app.customer.view.invoices.title') }}</h2>
              </div>
              <div class="text-end">
              </div>
            </div>
            <div class="">

              <table class="list-table">
                <thead>
                <tr>
                  <th>{{ $t('app.customer.view.invoices.list.amount') }}</th>
                  <th>{{ $t('app.customer.view.invoices.list.currency') }}</th>
                  <th>{{ $t('app.customer.view.invoices.list.status') }}</th>
                  <th>{{ $t('app.customer.view.invoices.list.created_at') }}</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="invoice in invoices">
                  <td>{{ currency(invoice.total) }}</td>
                  <td>{{ invoice.currency }}</td>
                  <td>
                    <span class="badge--green" v-if="invoice.paid">
                      {{ $t('app.customer.view.invoices.list.paid') }}
                    </span>
                    <span class="badge--red" v-else>
                      {{ $t('app.customer.view.invoices.list.outstanding') }}
                    </span></td>
                  <td>{{ $filters.moment(invoice.created_at, "LLL") || "unknown" }}</td>
                  <td><router-link :to="{name: 'app.invoices.view', params: {id: invoice.id}}" class="btn--main">{{ $t('app.customer.view.invoices.list.view_btn') }}</router-link></td>
                </tr>
                <tr v-if="invoices.length == 0">
                  <td colspan="5" class="text-center">{{ $t('app.customer.view.invoices.no_invoices') }}</td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
      </div>
      </div>


      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "CustomerView",
  components: {RoleOnlyView},
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
      limits: {},
      credit: [],
      invoices: [],
    }
  },
  methods: {
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
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
      axios.delete('/app/customer/'+customerId+'/payment-card/'+id).then(response => {
        for (var i = 0; i < this.paymentDetails.length; i++) {
          if (this.paymentDetails[i].id == id) {
            this.paymentDetails.splice(i, 1);
          }
        }
      })
    },
    defaultPayment: function (id) {
      var customerId = this.$route.params.id
      axios.post('/app/customer/'+customerId+'/payment-card/'+id+'/default').then(response => {
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
      this.credit = response.data.credit;
      this.invoices = response.data.invoices;
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