<template>
  <div>
    <h1 class="page-title mb-3">{{ $t('app.settings.stripe.view_import.title') }}</h1>
    <LoadingScreen :ready="ready">

      <div class="m-5 card-body">

        <dl class="detail-list">
          <div>
            <dt>{{ $t('app.settings.stripe.view_import.progress') }}</dt>
            <dd>
              <span class="font-bold" :class="{'text-blue-500': importData.state === 'started', 'text-red-500': importData.state != 'started'}">{{ $t('app.settings.stripe.view_import.process.started') }}</span> ->
              <span :class="{'text-blue-500  font-bold': importData.state === 'customers', 'text-red-500 font-bold': isCustomersDone(importData.state)}">{{ $t('app.settings.stripe.view_import.process.customers') }}</span> ->
              <span :class="{'text-blue-500  font-bold': importData.state === 'products', 'text-red-500 font-bold': isProductsDone(importData.state)}">{{ $t('app.settings.stripe.view_import.process.products') }}</span> ->
              <span :class="{'text-blue-500  font-bold': importData.state === 'prices', 'text-red-500 font-bold': isPricesDone(importData.state)}">{{ $t('app.settings.stripe.view_import.process.prices') }}</span> ->
              <span :class="{'text-blue-500  font-bold': importData.state === 'subscriptions', 'text-red-500 font-bold': isSubscriptionsDone(importData.state)}">{{ $t('app.settings.stripe.view_import.process.subscriptions') }}</span> ->
              <span :class="{'text-blue-500  font-bold': importData.state === 'payments', 'text-red-500 font-bold': isPaymentsDone(importData.state)}">{{ $t('app.settings.stripe.view_import.process.payments') }}</span> ->
              <span :class="{'text-blue-500  font-bold': importData.state === 'refunds', 'text-red-500 font-bold': isRefundsDone(importData.state)}">{{ $t('app.settings.stripe.view_import.process.refunds') }}</span> ->
              <span :class="{'text-blue-500  font-bold': importData.state === 'charge_backs', 'text-red-500 font-bold': isChargeBacksDone(importData.state)}">{{ $t('app.settings.stripe.view_import.process.charge_backs') }}</span> ->
              <span :class="{'text-red-500  font-bold': importData.state === 'completed'}">{{ $t('app.settings.stripe.view_import.process.completed') }}</span>
            </dd>
          </div>

          <div>
            <dt>{{ $t('app.settings.stripe.view_import.last_updated_at') }}</dt>
            <dd>{{ importData.updated_at }}</dd>
          </div>
          <div v-if="importData.last_id">
            <dt>{{ $t('app.settings.stripe.view_import.last_id_processed') }}</dt>
            <dd>{{ importData.last_id }}</dd>
          </div>
          <div v-if="importData.error">
            <dt>{{ $t('app.settings.stripe.view_import.error') }}</dt>
            <dd>{{ importData.error }}</dd>
          </div>
        </dl>
      </div>
    </LoadingScreen>

  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "StripeImportView",
  data() {
    return {
      ready: false,
      importData: {}
    }
  },
  methods: {
    isCustomersDone: function(state) {
      return state != 'customers' && state != 'started';
    },
    isProductsDone: function(state) {
      return state != 'customers' && state != 'started' && state != 'products';
    },
    isPricesDone: function(state) {
      return state != 'customers' && state != 'started' && state != 'products' && state != 'prices';
    },
    isSubscriptionsDone: function(state) {
      return state != 'customers' && state != 'started' && state != 'products' && state != 'prices' && state != 'subscriptions';
    },
    isPaymentsDone: function(state) {
      return state != 'customers' && state != 'started' && state != 'products' && state != 'prices' && state != 'subscriptions' && state != 'payments';
    },
    isRefundsDone: function(state) {
      return state != 'customers' && state != 'started' && state != 'products' && state != 'prices' && state != 'subscriptions' && state != 'payments'  && state != 'refunds';
    },
    isChargeBacksDone: function(state) {
      return state === 'completed';
    },
    loadData: function () {
      var that = this
      var id = this.$route.params.id
      axios.get('/app/settings/stripe-import/' + id + '/view').then(response => {
        this.importData = response.data;
        this.ready = true;
        setTimeout( function() {
          that.loadData();
        }, 5000);
      }).catch(error => {

          }
      );
    }
  },
  mounted() {
    this.loadData();
  }
}
</script>

<style scoped>

</style>