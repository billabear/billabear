<template>
  <div>
    <h1 class="page-title">{{ $t('app.payment.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="grid grid-cols-2 gap-3">
          <div class="mt-5">
            <h2 class="mb-3">{{ $t('app.payment.view.main.title') }}</h2>
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.payment.view.main.amount') }}</dt>
                <dd>{{ payment.amount }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.payment.view.main.currency') }}</dt>
                <dd>{{ payment.currency }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.payment.view.main.external_reference') }}</dt>
                <dd>
                  <a v-if="payment.payment_provider_details_url" target="_blank" :href="custpaymentomer.payment_provider_details_url">{{ payment.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                  <span v-else>{{ payment.external_reference }}</span>
                </dd>
              </div>
            </dl>

          </div>
          <div class="mt-5">

            <h2 class="mb-3">{{ $t('app.payment.view.customer.title') }}</h2>
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.payment.view.customer.email') }}</dt>
                <dd>{{ payment.customer.email }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.payment.view.customer.country') }}</dt>
                <dd>{{payment.customer.address.country }}</dd>
              </div>
              <div>
                <router-link :to="{name: 'app.customer.view', params: {id: payment.customer.id}}">{{ $t('app.payment.view.customer.more_info') }}</router-link>
              </div>
            </dl>
          </div>

          <div class="mt-5">
            <h2 class="mb-5">{{ $t('app.payment.view.refunds.title') }}</h2>

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
                  <td>{{ refund.amount }}</td>
                  <td>{{ refund.reason }}</td>
                  <td v-if="refund.billing_admin != null">{{ refund.billing_admin.display_name }}</td>
                  <td v-else>API</td>
                  <td>{{ $filters.moment(refund.created_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}</td>
                </tr>
                <tr v-if="refunds.length == 0">
                  <td colspan="4" class="text-center">{{ $t('app.payment.view.refunds.none') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="mt-5">
            <h2 class="mb-5">{{ $t('app.payment.view.subscriptions.title') }}</h2>

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
        </div>

        <div class="text-end mt-4">
          <button class="btn--main" @click="options.modelValue = true">{{ $t('app.payment.view.buttons.refund') }}</button>
        </div>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

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
      <div>
        <h3 class="mb-4 text-2xl font-semibold">{{ $t('app.payment.view.modal.refund.title') }}</h3>
        <div>
          <span class="block text-lg font-medium">{{ $t('app.payment.view.modal.refund.amount.title') }}</span>
          <span class="font-red" v-if="refundValues.errors.amount != undefined">{{ refundValues.errors.amount }}</span>
          <input type="number" v-model="refundValues.refundValue" class="form-field" />
        </div>
        <div class="mt-4">
          <span class="block text-lg font-medium">{{ $t('app.payment.view.modal.refund.reason.title') }}</span>
          <span class="font-red" v-if="refundValues.errors.reason != undefined">{{ refundValues.errors.reason }}</span>
          <input type="text" v-model="refundValues.reason" class="form-field" />
        </div>

        <div class="mt-4">
          <SubmitButton :in-progress="refundValues.inProgress" @click="sendRefund">{{ $t('app.payment.view.modal.refund.submit') }}</SubmitButton>
        </div>
      </div>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import {VueFinalModal} from "vue-final-modal";

export default {
  name: "PaymentView",
  components: {VueFinalModal},
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      payment: {},
      refunds: [],
      subscriptions: [],
      refundValues: {
        errors: {},
        refundValue: 0,
        reason: null,
        inProgress: false,
      },
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
    }
  },
  methods: {
    sendRefund: function () {
      const paymentId = this.$route.params.id
      const payload = {
        amount: this.refundValues.refundValue,
        reason: this.refundValues.reason,
      }
      this.refundValues.inProgress = true;
      axios.post('/app/payment/'+paymentId+'/refund', payload).then(response => {
        //this.refunds.push(response.data);
        this.refundValues.inProgress = false;
      }).catch(error => {
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