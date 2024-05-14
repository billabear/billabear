<template>
  <div>
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.payment.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="grid grid-cols-2 gap-3 p-5">
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.payment.view.main.title') }}</h2>
            <div class="section-body">

              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.payment.view.main.amount') }}</dt>
                  <dd>{{ currency(payment.amount) }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.payment.view.main.currency') }}</dt>
                  <dd>{{ payment.currency }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.payment.view.main.external_reference') }}</dt>
                  <dd>
                    <a v-if="payment.payment_provider_details_url" target="_blank" :href="payment.payment_provider_details_url">{{ payment.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    <span v-else>{{ payment.external_reference }}</span>
                  </dd>
                </div>
              </dl>
            </div>
          </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.payment.view.customer.title') }}</h2>
            <div class="section-body">
              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.payment.view.customer.email') }}</dt>
                  <dd v-if="payment.customer == null || payment.customer == undefined">N/A</dd>
                  <dd v-else>{{ payment.customer.email }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.payment.view.customer.country') }}</dt>
                  <dd v-if="payment.customer == null || payment.customer == undefined">N/A</dd>
                  <dd v-else>{{ payment.customer.address.country }}</dd>
                </div>
              </dl>
              <div v-if="payment.customer != null && payment.customer != undefined">
                <router-link :to="{name: 'app.customer.view', params: {id: payment.customer.id}}" class="btn--container">{{ $t('app.payment.view.customer.more_info') }}</router-link>
              </div>
              <div class="" v-else>
                <button @click="attachOptions.modelValue = true" class="btn--container">{{ $t('app.payment.view.customer.attach') }}</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <h2 class="mb-2">{{ $t('app.payment.view.refunds.title') }}</h2>

              <table class="list-table">
                <thead>
                <tr>
                  <th>{{ $t('app.payment.view.refunds.amount') }}</th>
                  <th>{{ $t('app.payment.view.refunds.reason') }}</th>
                  <th>{{ $t('app.payment.view.refunds.created_by') }}</th>
                  <th>{{ $t('app.payment.view.refunds.created_at') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="refund in refunds">
                  <td>{{ currency(refund.amount) }}</td>
                  <td>{{ refund.reason }}</td>
                  <td v-if="refund.billing_admin != null">{{ refund.billing_admin.display_name }}</td>
                  <td v-else>API</td>
                  <td>{{ $filters.moment(refund.created_at, "lll") || "unknown" }}</td>
                </tr>
                <tr v-if="refunds.length == 0">
                  <td colspan="4" class="text-center">{{ $t('app.payment.view.refunds.none') }}</td>
                </tr>
                </tbody>
              </table>
          </div>
          <div class="card-body">
            <h2 class="mb-2">{{ $t('app.payment.view.subscriptions.title') }}</h2>

            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.payment.view.subscriptions.plan_name') }}</th>
                <th>{{ $t('app.payment.view.subscriptions.more_info') }}</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="subscription in subscriptions">
                <td>{{ subscription.plan.name }}</td>
                <td><router-link :to="{name: 'app.subscription.view', params: {subscriptionId: subscription.id}}" class="btn--main">{{ $t('app.payment.view.subscriptions.more_info') }}</router-link></td>
              </tr>
              <tr v-if="subscriptions.length == 0">
                <td colspan="2" class="text-center">{{ $t('app.payment.view.subscriptions.none') }}</td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="card-body">
            <h2 class="mb-2">{{ $t('app.payment.view.receipts.title') }}</h2>

              <table class="list-table">
                <thead>
                <tr>
                  <th>{{ $t('app.payment.view.receipts.created_at') }}</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="receipt in receipts">
                  <td>{{ $filters.moment(receipt.created_at, "lll") || "unknown" }}</td>
                  <td><a :href="'/app/receipt/'+receipt.id+'/download'" class="btn--main">{{ $t('app.payment.view.receipts.download') }}</a></td>
                </tr>
                <tr v-if="receipts.length == 0">
                  <td colspan="2" class="text-center">{{ $t('app.payment.view.receipts.none') }}</td>
                </tr>
                </tbody>
              </table>
          </div>
        </div>

        <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
          <div class="text-end m-5">
            <SubmitButton :in-progress="generatingReceipt" button-class="btn--secondary mr-3" @click="generateReceipt">{{ $t('app.payment.view.buttons.generate_receipt') }}</SubmitButton>
            <button class="btn--main" @click="refundSent = false;options.modelValue = true">{{ $t('app.payment.view.buttons.refund') }}</button>
          </div>
        </RoleOnlyView>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

    <VueFinalModal
        v-model="attachOptions.modelValue"
        :teleport-to="attachOptions.teleportTo"
        :display-directive="attachOptions.displayDirective"
        :hide-overlay="attachOptions.hideOverlay"
        :overlay-transition="attachOptions.overlayTransition"
        :content-transition="attachOptions.contentTransition"
        :click-to-close="attachOptions.clickToClose"
        :esc-to-close="attachOptions.escToClose"
        :background="attachOptions.background"
        :lock-scroll="attachOptions.lockScroll"
        :swipe-to-close="attachOptions.swipeToClose"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <h1>{{ $t('app.payment.view.modal.attach.title') }}</h1>
      <Autocomplete search-key="email" rest-endpoint="/app/customer" display-key="email" v-model="attachCustomer" />

      <SubmitButton @click="sendAttachToCustomer" :in-progress="attachInProgress">{{ $t('app.payment.view.modal.attach.button') }}</SubmitButton>
    </VueFinalModal>
    <VueFinalModal
        v-model="options.modelValue"
        :teleport-to="options.teleportTo"
        :display-directive="options.displayDirective"
        :hide-overlay="options.hideOverlay"
        :overlay-transition="options.overlayTransition"
        :content-transition="options.contentTransition"
        :click-to-close="options.clickToClose"
        :esc-to-close="options.escToClose"
        :background="options.background"
        :lock-scroll="options.lockScroll"
        :swipe-to-close="options.swipeToClose"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <div v-if="!refundSent">
        <h3 class="mb-4 text-2xl font-semibold">{{ $t('app.payment.view.modal.refund.title') }}</h3>
        <div>
          <span class="block text-lg font-medium">{{ $t('app.payment.view.modal.refund.amount.title') }}</span>
          <p class="text-red-500" v-if="refundValues.errors.amount != undefined">{{ refundValues.errors.amount }}</p>
          <input type="number" v-model="refundValues.refundValue" class="form-field" />
        </div>
        <div class="mt-4">
          <span class="block text-lg font-medium">{{ $t('app.payment.view.modal.refund.reason.title') }}</span>
          <p class="text-red-500" v-if="refundValues.errors.reason != undefined">{{ refundValues.errors.reason }}</p>
          <input type="text" v-model="refundValues.reason" class="form-field" />
        </div>

        <div class="mt-4">
          <SubmitButton :in-progress="refundValues.inProgress" @click="sendRefund">{{ $t('app.payment.view.modal.refund.submit') }}</SubmitButton>
        </div>
      </div>
      <div v-else-if="refundSuccess">
        <div class="text-center text-green-500 text-6xl mb-5">
          <i class="fa-solid fa-circle-check "></i>
        </div>
        <p class="text-center">{{ $t('app.payment.view.modal.refund.success_message') }}</p>
      </div>
      <div v-else>
        <div class="text-center text-red-500 text-6xl mb-5">
          <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <p class="text-center">{{ $t('app.payment.view.modal.refund.error_message') }}</p>
      </div>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import {VueFinalModal} from "vue-final-modal";
import currency from "currency.js";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";
import Autocomplete from "../../../components/app/Forms/Autocomplete.vue";

export default {
  name: "PaymentView",
  components: {Autocomplete, RoleOnlyView, VueFinalModal},
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      payment: {},
      refunds: [],
      receipts: [],
      subscriptions: [],
      refundSent: false,
      refundSuccess: false,
      refundValues: {
        errors: {},
        refundValue: 0,
        reason: null,
        inProgress: false,
      },
      generatingReceipt: false,
      options: {
        teleportTo: 'body',
        modelValue: false,
        displayDirective: 'if',
        hideOverlay: false,
        overlayTransition: 'vfm-fade',
        contentTransition: 'vfm-fade',
        clickToClose: true,
        escToClose: true,
        background: 'non-interactive',
        lockScroll: true,
        swipeToClose: 'none',
      },
      attachCustomer: null,
      attachInProgress: false,
      attachOptions: {
        teleportTo: 'body',
        modelValue: false,
        displayDirective: 'if',
        hideOverlay: false,
        overlayTransition: 'vfm-fade',
        contentTransition: 'vfm-fade',
        clickToClose: true,
        escToClose: true,
        background: 'non-interactive',
        lockScroll: true,
        swipeToClose: 'none',
      },
    }
  },
  methods: {
    sendAttachToCustomer() {
      const paymentId = this.$route.params.id
      this.attachInProgress = true;
      axios.post("/app/payment/"+paymentId+"/attach", {customer: this.attachCustomer}).then(response => {
        this.attachInProgress = false;
        this.payment.customer = response.data;
        this.attachOptions.modelValue = false;
      })
    },
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
    generateReceipt: function (){
      this.generatingReceipt = true;
      const paymentId = this.$route.params.id
      axios.post('/app/payment/'+paymentId+'/generate-receipt').then(response => {
        this.generatingReceipt = false;
        this.receipts.push(response.data)
      }).catch(error => {
        this.generatingReceipt = false;
      })
    },
    sendRefund: function () {
      const paymentId = this.$route.params.id
      const payload = {
        amount: this.refundValues.refundValue,
        reason: this.refundValues.reason,
      }
      this.refundValues.inProgress = true;
      this.refundValues.errors = {};
      axios.post('/app/payment/'+paymentId+'/refund', payload).then(response => {
        this.refunds.push(response.data);
        this.refundValues.inProgress = false;
        this.refundSent = true;
        this.refundSuccess = true;
      }).catch(error => {
        if (error.response.data.errors === undefined) {
          this.refundSent = true;
          this.refundSuccess = false;
          return;
        }

        this.refundValues.errors = error.response.data.errors;
        this.refundValues.inProgress = false;
      })
    }
  },
  mounted() {
    var paymentId = this.$route.params.id
    axios.get('/app/payments/'+paymentId).then(response => {
      this.payment = response.data.payment;
      this.refunds = response.data.refunds;
      this.subscriptions = response.data.subscriptions;
      this.refundValues.refundValue = response.data.max_refundable;
      this.receipts = response.data.receipts;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
          this.errorMessage = this.$t('app.payment.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.payment.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>