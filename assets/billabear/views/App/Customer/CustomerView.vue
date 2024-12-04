<template>
  <div>
    <div class="grid grid-cols-1 md:grid-cols-2">
      <h1 class="page-title">{{ $t('app.customer.view.title') }}</h1>
      <div class="text-end mt-3">

        <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
          <div class="">
            <button class="btn--danger mr-3" v-if="customer.status == 'disabled'" @click="enableCustomer">{{ $t('app.customer.view.enable') }}</button>
            <button class="btn--danger mr-3" v-else @click="disableCustomer">{{ $t('app.customer.view.disable') }}</button>
            <router-link :to="{name: 'app.customer.update'}" class="btn--main">{{ $t('app.customer.view.update') }}</router-link>
          </div>
        </RoleOnlyView>
      </div>
    </div>

    <LoadingScreen :ready="ready">
      <div v-if="!error">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div class="card-body">
            <h2 class="text-xl font-bold mb-3">{{ $t('app.customer.view.main.title') }}</h2>
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
                  <dt>{{ $t('app.customer.view.address.company_name') }}</dt>
                  <dd>{{ customer.address.company_name }}</dd>
                </div>
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
                  <dd>{{ customer.address.postcode }}</dd>
                </div>
              </dl>
            </div>
          </div>

          <div class="card-body">
            <SubscriptionList :result-set="subscriptions" :customer="customer" />
          </div>

          <div class="card-body">
            <h2  class="section-header">{{ $t('app.customer.view.metric_counters.title') }}</h2>

            <div class="mt-2">

              <table class="list-table">
                <thead>
                <tr>
                  <th>{{ $t('app.customer.view.metric_counters.list.name') }}</th>
                  <th>{{ $t('app.customer.view.metric_counters.list.usage') }}</th>
                  <th>{{ $t('app.customer.view.metric_counters.list.cost') }}</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                  <tr v-for="metric_counter in metric_counters">
                    <td>{{ metric_counter.metric.code }}</td>
                    <td>{{ metric_counter.usage}}</td>
                    <td>{{ currency(metric_counter.estimated_cost) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="card-body">

            <div class="grid grid-cols-1 md:grid-cols-2">
              <div>
                <h2  class="section-header">{{ $t('app.customer.view.usage_limits.title') }}</h2>
              </div>
              <div class="text-end">
                <button class="btn--main" @click="show_create_usage_limits = true">{{ $t('app.customer.view.usage_limits.add_new') }}</button>
              </div>
            </div>
            <div class="mt-2">
              <table class="list-table">
                <thead>
                <tr>
                  <th>{{ $t('app.customer.view.usage_limits.list.amount') }}</th>
                  <th>{{ $t('app.customer.view.usage_limits.list.warn_level') }}</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                  <tr v-for="(limit, key) in usage_limits">
                    <td><Currency :amount="limit.amount" /></td>
                    <td>
                      <span v-if="limit.warn_level === 1000">{{ $t('app.customer.view.usage_limits.warn_levels.warn') }}</span>
                      <span v-else-if="limit.warn_level === 9999">{{ $t('app.customer.view.usage_limits.warn_levels.disable') }}</span>
                    </td>
                    <td><button class="btn--danger" @click="deleteLimit(key)"><i class="fa-solid fa-trash"></i></button></td>
                  </tr>
                  <tr v-if="usage_limits.length === 0">
                    <td colspan="3" class="text-center">{{ $t('app.customer.view.usage_limits.no_limits') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <VueFinalModal
                v-model="show_create_usage_limits"
                class="flex justify-center items-center"
                content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
            >
              <UsageLimitAdd :customer="customer" :limits="usage_limits" @close-modal="closeAddModal" />
            </VueFinalModal>
          </div>

          <div class="card-body">
            <CustomerSubscriptionEvent :subscription_events="subscription_events" />
          </div>


          <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2">
              <div><h2  class="section-header">{{ $t('app.customer.view.payment_details.title') }}</h2></div>
              <div class="text-end">
                <RoleOnlyView role="ROLE_DEVELOPER">
                  <router-link class="btn--secondary mr-2" :to="{name: 'app.customer.payment_details.token', params: {customerId: customer.id}}">{{ $t('app.customer.view.payment_details.add_token') }}</router-link>
                </RoleOnlyView>
                <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
                  <router-link class="btn--main" :to="{name: 'app.customer.payment_details.add', params: {customerId: customer.id}}">{{ $t('app.customer.view.payment_details.add_new') }}</router-link>
                </RoleOnlyView>
              </div>
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
            <CustomerPaymentList :result-set="payments" :customer="customer" />
          </div>

          <div class="card-body">
            <CustomerRefundList :result-set="refunds" :customer="customer" />
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
            <CustomerInvoiceList :invoice-result="invoices" :customer="customer" />
          </div>
          <div class="card-body">
            <CustomerInvoiceDelivery :invoice-result="invoice_delivery" :customer="customer" />
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
import SubscriptionList from "../../../components/app/Customer/View/SubscriptionList.vue";
import CustomerInvoiceList from "../../../components/app/Customer/View/CustomerInvoiceList.vue";
import CustomerSubscriptionEvent from "../../../components/app/Customer/View/CustomerSubscriptionEvent.vue";
import CustomerPaymentList from "../../../components/app/Customer/View/CustomerPaymentList.vue";
import CustomerRefundList from "../../../components/app/Customer/View/CustomerRefundList.vue";
import CustomerInvoiceDelivery from "../../../components/app/Customer/View/CustomerInvoiceDelivery.vue";
import Currency from "../../../components/app/Currency.vue";
import {Button} from "flowbite-vue";
import UsageLimitAdd from "../../../components/app/Customer/Modal/UsageLimitAdd.vue";
import {VueFinalModal} from "vue-final-modal";
import {handleResponse} from "../../../services/utils";

export default {
  name: "CustomerView",
  components: {
    VueFinalModal,
    UsageLimitAdd,
    Button,
    Currency,
    CustomerInvoiceDelivery,
    CustomerRefundList,
    CustomerPaymentList, CustomerSubscriptionEvent, CustomerInvoiceList, SubscriptionList, RoleOnlyView},
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
      subscription_events: [],
      invoice_delivery: {},
      metric_counters: [],
      usage_limits: [],
      show_create_usage_limits: false,
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
      const customerId = this.$route.params.id
      axios.post('/app/customer/'+customerId+'/payment-card/'+id+'/default').then(response => {
        for (var i = 0; i < this.paymentDetails.length; i++) {
          if (this.paymentDetails[i].id == id) {
            this.paymentDetails[i].default = true;
          } else {
            this.paymentDetails[i].default = false;
          }
        }
      })
    },
    closeAddModal: function () {
      this.show_create_usage_limits = false;
    },
    deleteLimit: function (key) {
      const customerId = this.$route.params.id
      const limit = this.usage_limits[key];
      axios.post('/app/customer/'+customerId+'/usage-limit/'+limit.id+'/delete').then(response => {
        this.usage_limits.splice(key, 1);
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
      this.subscription_events = response.data.subscription_events;
      this.invoice_delivery = response.data.invoice_delivery;
      this.metric_counters = response.data.metric_counters;
      this.usage_limits = response.data.usage_limits;
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
