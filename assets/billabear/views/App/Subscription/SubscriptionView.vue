<template>
  <div>
    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <h1 class="mt-5 ml-5 page-title">{{ $t('app.subscription.view.title') }}</h1>
        <div class="grid grid-cols-2 gap-3 p-5">
        <div class="card-body">
          <h2 class="section-header">{{ $t('app.subscription.view.main.title') }}</h2>
          <dl class="detail-list section-body ">
            <div>
              <dt>{{ $t('app.subscription.view.main.status') }}</dt>
              <dd>{{ subscription.status }}</dd>
            </div>

            <div v-if="subscription.plan !== null && subscription.plan !== undefined">
              <dt>{{ $t('app.subscription.view.main.plan') }}</dt>
              <dd>
                <router-link :to="{name: 'app.subscription_plan.view', params: {productId: product.id, subscriptionPlanId: subscription.plan.id}}">
                  {{ subscription.plan.name }}
                </router-link>
                <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
                  <button class="btn--main ml-3" @click="showPlan">{{ $t('app.subscription.view.main.plan_change') }}</button>
                </RoleOnlyView>
              </dd>
            </div>
            <div v-else></div>
            <div>
              <dt>{{ $t('app.subscription.view.main.customer') }}</dt>
              <dd>
                <router-link :to="{name: 'app.customer.view', params: {id: customer.id}}">
                  {{ customer.email }}
                </router-link>
              </dd>
            </div>
            <div>
              <dt>{{ $t('app.subscription.view.main.main_external_reference') }}</dt>
              <dd>
                <a v-if="subscription.external_main_reference_details_url" target="_blank" :href="subscription.external_main_reference_details_url">{{ subscription.main_external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <span v-else>{{ subscription.main_external_reference }}</span>
              </dd>
            </div>
            <div>
              <dt>{{ $t('app.subscription.view.main.created_at') }}</dt>
              <dd> {{ $filters.moment(subscription.created_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}
              </dd>
            </div>
            <div v-if="subscription.ended_at != null">
              <dt>{{ $t('app.subscription.view.main.ended_at') }}</dt>
              <dd> {{ $filters.moment(subscription.ended_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}
              </dd>
            </div>
            <div v-else>
              <dt>{{ $t('app.subscription.view.main.valid_until') }}</dt>
              <dd> {{ $filters.moment(subscription.valid_until, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}
              </dd>
            </div>
            <div v-if="subscription.plan.per_seat == true">
              <dt>{{ $t('app.subscription.view.main.seat_number') }}</dt>
              <dd>
                {{ subscription.seat_number }}
                <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
                  <button class="btn--main ml-3" @click="showSeatChange">{{ $t('app.subscription.view.main.change_seat') }}</button>
                </RoleOnlyView>
              </dd>
            </div>
          </dl>
        </div>
        <div class="card-body">
          <h2 class="section-header">{{ $t('app.subscription.view.pricing.title') }}</h2>
          <dl class="detail-list section-body">
            <div>
              <dt>{{ $t('app.subscription.view.pricing.price') }}</dt>
              <dd>{{ subscription.price.display_value }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.subscription.view.pricing.recurring') }}</dt>
              <dd>{{ subscription.price.recurring }}</dd>
            </div>
            <div v-if="subscription.price.recurring">
              <dt>{{ $t('app.subscription.view.pricing.schedule') }}</dt>
              <dd>{{ subscription.price.schedule }}</dd>
            </div>
          </dl>
          <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
            <div class="mt-2">
              <button class="btn--container" @click="showPrice">{{ $t('app.subscription.view.pricing.change') }}</button>
            </div>
          </RoleOnlyView>
        </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.subscription.view.payment_method.title') }}</h2>
            <dl class="detail-list section-body" v-if="paymentDetails !== null && paymentDetails !== undefined">
              <div>
                <dt>{{ $t('app.subscription.view.payment_method.last_four') }}</dt>
                <dd>**** **** **** {{ paymentDetails.last_four }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription.view.payment_method.brand') }}</dt>
                <dd>{{ paymentDetails.brand }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription.view.payment_method.expiry_month') }}</dt>
                <dd>{{ paymentDetails.expiry_month }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.subscription.view.payment_method.expiry_year') }}</dt>
                <dd>{{ paymentDetails.expiry_year }}</dd>
              </div>
            </dl>
            <div v-else class="text-center section-body">
              {{ $t('app.subscription.view.payment_method.invoiced') }}
            </div>
          </div>
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.subscription.view.payments.title') }}</h2>
            <div class="section-body">

              <table class="list-table">
                <thead>
                <tr>
                  <th>{{ $t('app.subscription.view.payments.amount') }}</th>
                  <th>{{ $t('app.subscription.view.payments.created_at') }}</th>
                  <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="payments.length == 0">
                  <td colspan="3" class="text-center">{{ $t('app.subscription.view.payments.no_payments') }}</td>
                </tr>
                <tr v-for="payment in payments">
                  <td>{{ currency(payment.amount) }}</td>
                  <td>{{ payment.created_at }}</td>
                  <td><router-link :to="{name: 'app.payment.view', params: {id: payment.id}}" class="btn--main">{{ $t('app.subscription.view.payments.view') }}</router-link></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="mt-5 mr-5 text-end">

          <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
            <button class="btn--secondary mr-2" @click="showChangePaymentMethods" v-if="paymentDetails !== null && paymentDetails !== undefined">
              {{ $t('app.subscription.view.buttons.payment_method') }}
            </button>

            <button class="btn--danger" @click="options.modelValue = true" :class="{'btn--danager--disabled': subscription.status == 'cancelled'}" :disabled="subscription.status == 'cancelled'">
              {{ $t('app.subscription.view.buttons.cancel') }}
            </button>
          </RoleOnlyView>
        </div>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

    <VueFinalModal
        v-model="planOptions.modelValue"
        :teleport-to="planOptions.teleportTo"
        :display-directive="planOptions.displayDirective"
        :hide-overlay="planOptions.hideOverlay"
        :overlay-transition="planOptions.overlayTransition"
        :content-transition="planOptions.contentTransition"
        :click-to-close="planOptions.clickToClose"
        :esc-to-close="planOptions.escToClose"
        :background="planOptions.background"
        :lock-scroll="planOptions.lockScroll"
        :swipe-to-close="planOptions.swipeToClose"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <LoadingMessage v-if="!planReady" />
      <div v-else>

        <div>
          <span class="block text-lg font-medium">{{ $t('app.subscription.view.modal.plan.when.title') }}</span>
          <p class="text-red-500" v-if="planErrors.when != undefined">{{ planErrors.when }}</p>
          <select v-model="planWhen" class="form-field">
            <option value="next-cycle">{{ $t('app.subscription.view.modal.plan.when.next_cycle') }}</option>
            <option value="instantly">{{ $t('app.subscription.view.modal.plan.when.instantly') }}</option>
          </select>
        </div>
        <div class="">
          <label class="form-field-lbl" for="price">
            {{ $t('app.subscription.view.modal.plan.plan') }}
          </label>
          <p class="text-red-500" v-if="planErrors.planId != undefined">{{ planErrors.planId }}</p>
          <select class="form-field" v-model="newPlan">
            <option v-for="plan in plans" :value="plan">{{ plan.name }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.subscription.view.modal.plan.plan_help') }}</p>
        </div>
        <div v-if="newPlan.id !== undefined && newPlan.id !== null">
          <label class="form-field-lbl" for="price">
            {{ $t('app.subscription.view.modal.plan.price') }}
          </label>
          <p class="text-red-500" v-if="planErrors.priceId != undefined">{{ planErrors.priceId }}</p>
          <select class="form-field" v-model="newPrice">
            <option v-for="price in newPlan.prices" :value="price">{{ price.display_value }} - {{ price.schedule }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.subscription.view.modal.plan.price_help') }}</p>
        </div>
        <div class="mt-4">
          <SubmitButton :in-progress="planSending" @click="sendPlan">{{ $t('app.subscription.view.modal.plan.submit') }}</SubmitButton>
        </div>
      </div>

    </VueFinalModal>

    <VueFinalModal
        v-model="seatOptions.modelValue"
        :teleport-to="seatOptions.teleportTo"
        :display-directive="seatOptions.displayDirective"
        :hide-overlay="seatOptions.hideOverlay"
        :overlay-transition="seatOptions.overlayTransition"
        :content-transition="seatOptions.contentTransition"
        :click-to-close="seatOptions.clickToClose"
        :esc-to-close="seatOptions.escToClose"
        :background="seatOptions.background"
        :lock-scroll="seatOptions.lockScroll"
        :swipe-to-close="priceOptions.swipeToClose"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >

        <div class="">
          <label class="form-field-lbl" for="price">
            {{ $t('app.subscription.view.modal.seats.seats') }}
          </label>
          <input type="number" class="form-field" v-model="subscription.seat_number" />
          <p class="form-field-help">{{ $t('app.subscription.view.modal.seats.seats_help') }}</p>
        </div>
        <div class="mt-4">
          <SubmitButton :in-progress="seatSending" @click="sendSeats">{{ $t('app.subscription.view.modal.seats.submit') }}</SubmitButton>
        </div>
    </VueFinalModal>

    <VueFinalModal
        v-model="priceOptions.modelValue"
        :teleport-to="priceOptions.teleportTo"
        :display-directive="priceOptions.displayDirective"
        :hide-overlay="priceOptions.hideOverlay"
        :overlay-transition="priceOptions.overlayTransition"
        :content-transition="priceOptions.contentTransition"
        :click-to-close="priceOptions.clickToClose"
        :esc-to-close="priceOptions.escToClose"
        :background="priceOptions.background"
        :lock-scroll="priceOptions.lockScroll"
        :swipe-to-close="priceOptions.swipeToClose"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <LoadingMessage v-if="!priceReady" />
      <div v-else>

        <div class="">
          <label class="form-field-lbl" for="price">
            {{ $t('app.subscription.view.modal.price.price') }}
          </label>
          <select class="form-field" v-model="newPrice">
            <option v-for="price in prices" :value="price">{{ price.display_value }} {{ price.schedule }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.subscription.view.modal.price.price_help') }}</p>
        </div>
        <div class="mt-4">
          <SubmitButton :in-progress="priceSending" @click="sendPrice">{{ $t('app.subscription.view.modal.price.submit') }}</SubmitButton>
        </div>
      </div>
    </VueFinalModal>

    <VueFinalModal
        v-model="paymentMethodOptions.modelValue"
        :teleport-to="paymentMethodOptions.teleportTo"
        :display-directive="paymentMethodOptions.displayDirective"
        :hide-overlay="paymentMethodOptions.hideOverlay"
        :overlay-transition="paymentMethodOptions.overlayTransition"
        :content-transition="paymentMethodOptions.contentTransition"
        :click-to-close="paymentMethodOptions.clickToClose"
        :esc-to-close="paymentMethodOptions.escToClose"
        :background="paymentMethodOptions.background"
        :lock-scroll="paymentMethodOptions.lockScroll"
        :swipe-to-close="paymentMethodOptions.swipeToClose"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <LoadingMessage v-if="!paymentMethodReady" />
      <div v-else>

        <div class="">
          <label class="form-field-lbl" for="street_line_two">
            {{ $t('app.subscription.view.modal.payment_method.payment_method') }}
          </label>
          <select class="form-field" v-model="newPaymentMethod">
            <option v-for="paymentMethod in paymentMethods" :value="paymentMethod">{{ paymentMethod.brand }} - **** **** **** {{ paymentMethod.last_four }} - {{paymentMethod.expiry_month }}/{{paymentMethod.expiry_year }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.subscription.view.modal.payment_method.payment_method_help') }}</p>
        </div>
        <div class="mt-4">
          <SubmitButton :in-progress="paymentMethodsSending" @click="sendChangePaymentMethods">{{ $t('app.subscription.view.modal.payment_method.submit') }}</SubmitButton>
        </div>
      </div>

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
      <div>
        <h3 class="mb-4 text-2xl font-semibold">{{ $t('app.subscription.view.modal.cancel.title') }}</h3>
        <div>
          <span class="block text-lg font-medium">{{ $t('app.subscription.view.modal.cancel.when.title') }}</span>
          <span class="font-red" v-if="cancelValues.errors.when != undefined">{{ cancelValues.errors.when }}</span>
          <select v-model="cancelValues.when" class="form-field">
            <option value="end-of-run">{{ $t('app.subscription.view.modal.cancel.when.end_of_run') }}</option>
            <option value="instantly">{{ $t('app.subscription.view.modal.cancel.when.instantly') }}</option>
            <option value="specific-date">{{ $t('app.subscription.view.modal.cancel.when.specific_date') }}</option>
          </select>
        </div>

        <span class="font-red" v-if="cancelValues.errors.date != undefined">{{ cancelValues.errors.date }}</span>
        <VueDatePicker  class="mt-2" v-model="cancelValues.date"  :enable-time-picker="false" v-if="cancelValues.when == 'specific-date'"></VueDatePicker>
        <div class="mt-2">

          <span class="block text-lg font-medium">{{ $t('app.subscription.view.modal.cancel.refund_type.title') }}</span>
          <span class="font-red" v-if="cancelValues.errors.refundType != undefined">{{ cancelValues.errors.refundType }}</span>
          <select v-model="cancelValues.refundType"  class="form-field" :disabled="cancelValues.when == 'end-of-run'">
            <option value="none">{{ $t('app.subscription.view.modal.cancel.refund_type.none') }}</option>
            <option value="prorate">{{ $t('app.subscription.view.modal.cancel.refund_type.prorate') }}</option>
            <option value="full">{{ $t('app.subscription.view.modal.cancel.refund_type.full') }}</option>
          </select>
        </div>

        <div class="mt-5 text-center" v-if="cancelValues.cancelled == false">
          <button class="btn--secondary mr-3" @click="options.modelValue = false">{{ $t('app.subscription.view.modal.cancel.close_btn') }}</button>
          <SubmitButton @click="sendCancel" :in-progress="cancelSending">{{ $t('app.subscription.view.modal.cancel.cancel_btn') }}</SubmitButton>
        </div>
        <div class="mt-5 text-center" v-else>
          {{ $t('app.subscription.view.modal.cancel.cancelled_message') }}
        </div>
      </div>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import {useModal, VueFinalModal} from "vue-final-modal";
import currency from "currency.js";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "SubscriptionView",
  components: {RoleOnlyView, VueFinalModal},
  data() {
    return {
      subscription: {},
      customer: {},
      product: {},
      paymentDetails: {},
      payments: [],
      refunds: [],
      ready: false,
      error: false,
      errorMessage: undefined,
      cancelValues: {
          when: "end-of-run",
          refundType: "none",
          date: null,
          errors: {},
          cancelled: false
      },
      seatOptions: {
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
      cancelSending: false,
      planErrors: {},
      planWhen: null,
      planSending: false,
      planReady: false,
      planOptions: {
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
      newPlan: {},
      newPrice: {id: null},
      priceSending: false,
      priceReady: false,
      priceOptions: {
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
      paymentMethodOptions: {
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
      paymentMethodReady: false,
      paymentMethods: [],
      newPaymentMethod: {},
      paymentMethodsSending: false,
      seatSending: false,
    };
  },
  mounted() {
    var subscriptionId = this.$route.params.subscriptionId
    axios.get('/app/subscription/' + subscriptionId).then(response => {
      this.product = response.data.product;
      this.subscription = response.data.subscription;
      this.customer = response.data.customer;
      this.paymentDetails = response.data.payment_details;
      this.payments = response.data.payments;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
        this.errorMessage = this.$t('app.subscription.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.subscription.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  },
  methods: {
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
    showPlan: function () {
      this.planOptions.modelValue = true;
      var subscriptionId = this.$route.params.subscriptionId

      axios.get('/app/subscription/' + subscriptionId+'/change-plan').then(response => {
        this.plans = response.data.plans;
        this.planReady = true;
      })
    },
    sendPlan: function () {
        this.planSending = true;
        const subscriptionId = this.$route.params.subscriptionId;
        const payload = {
          when: this.planWhen,
          price: this.newPrice.id,
          plan: this.newPlan.id,
        };
        axios.post('/app/subscription/'+subscriptionId+'/change-plan', payload).then(response => {
          this.planOptions.modelValue = false;
          this.subscription.plan = this.newPlan;
          this.subscription.price = this.newPrice;
        }).catch(error => {
          this.planErrors = error.response.data.errors;
          this.planSending = true;
        })
    },
    showPrice: function () {
      this.priceOptions.modelValue = true;
      var subscriptionId = this.$route.params.subscriptionId

      axios.get('/app/subscription/' + subscriptionId+'/price').then(response => {
          this.newPrice = this.subscription.price;
          this.prices = response.data.data;
          this.priceReady = true;
      })
    },
    showSeatChange: function () {
        this.seatOptions.modelValue = true;
    },
    sendSeats: function () {

      var subscriptionId = this.$route.params.subscriptionId
      this.seatSending = true;
      axios.post('/app/subscription/' + subscriptionId+'/seats/set', {seats: this.subscription.seat_number}).then(response => {
        this.seatSending = false;
        this.seatOptions.modelValue = false;
      })
    },
    sendPrice: function () {

      var subscriptionId = this.$route.params.subscriptionId
      this.priceSending = true;
      axios.post('/app/subscription/' + subscriptionId+'/price', {price: this.newPrice.id}).then(response => {
        this.priceSending = false;
      })
    },
    showChangePaymentMethods: function () {
        this.paymentMethodOptions.modelValue = true;
        axios.get('/app/customer/'+this.customer.id+'/payment-card').then(response => {
            this.newPaymentMethod = this.paymentDetails;
            this.paymentMethods = response.data.data;
            this.paymentMethodReady = true;

        })
    },
    sendChangePaymentMethods: function () {
        this.paymentMethodsSending = true;
        var subscriptionId = this.$route.params.subscriptionId
        const payload = {
          payment_details: this.newPaymentMethod.id,
        };
        axios.post('/app/subscription/' + subscriptionId+'/payment-card', payload).then(response => {
          this.paymentMethodsSending = false;
          this.paymentDetails = this.newPaymentMethod;
        })
    },
    sendCancel: function () {
      this.cancelSending = true
      var subscriptionId = this.$route.params.subscriptionId
      const payload = {
        when: this.cancelValues.when,
        date: this.cancelValues.date,
        refund_type: this.cancelValues.refundType,
      }
      axios.post('/app/subscription/' + subscriptionId+'/cancel', payload).then(response => {
        this.cancelSending = false;
        this.cancelValues.cancelled = true;
      }).toLocaleString(error => {
        this.cancelValues.errors = error.response.data.errors;
        this.cancelSending = false;
      })
    }
  }
}
</script>

<style scoped>
</style>