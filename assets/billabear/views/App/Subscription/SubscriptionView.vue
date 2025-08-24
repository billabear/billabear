<template>
  <div>
    <LoadingScreen :ready="!loading">
      <div v-if="!error">
        <h1 class="page-title">{{ $t('app.subscription.view.title') }}</h1>
        <div class="grid grid-cols-2 gap-3">
          <SubscriptionDetails 
            :subscription="subscription"
            :customer="customer"
            :product="product"
            @show-plan="planModal.openModal"
            @show-seat-change="seatModal.openModal"
          />
          <SubscriptionPricing 
            :subscription="subscription"
            @show-price="priceModal.openModal"
          />
          <SubscriptionPaymentMethod 
            :payment-details="paymentDetails"
          />
          <SubscriptionPaymentsTable :payments="payments" />
          <SubscriptionEventsTable :subscription-events="subscriptionEvents" />
          <SubscriptionMetadata :subscription="subscription" />

          <div class="card-body" v-if="usageEstimate !== null && usageEstimate !== undefined">
            <div>
              <h2  class="section-header">{{ $t('app.subscription.view.usage_estimate.title') }}</h2>
              <dl class="detail-list section-body">
                <div>
                  <dt>{{ $t('app.subscription.view.usage_estimate.metric') }}</dt>
                  <dd>{{ usageEstimate.metric.name }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.subscription.view.usage_estimate.usage') }}</dt>
                  <dd>{{ usageEstimate.usage }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.subscription.view.usage_estimate.estimate_cost') }}</dt>
                  <dd><Currency :amount="usageEstimate.amount" /></dd>
                </div>
              </dl>
            </div>

          </div>
        </div>
        <div class="mt-5 mr-5 text-end">

          <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
            <router-link :to="{name: 'app.compliance.audit.subscription', params: {id: subscription.id}}" class="btn--main">{{ $t('app.subscription.view.buttons.audit_log') }}</router-link>
          </RoleOnlyView>
          <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
            <button class="btn--secondary mx-2" @click="showChangePaymentMethods" v-if="paymentDetails !== null && paymentDetails !== undefined">
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
import { onMounted } from 'vue'
import { VueFinalModal } from "vue-final-modal";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";
import Currency from "../../../components/app/Currency.vue";
import SubscriptionDetails from "../../../components/app/Subscription/SubscriptionDetails.vue";
import SubscriptionPricing from "../../../components/app/Subscription/SubscriptionPricing.vue";
import SubscriptionPaymentMethod from "../../../components/app/Subscription/SubscriptionPaymentMethod.vue";
import SubscriptionPaymentsTable from "../../../components/app/Subscription/SubscriptionPaymentsTable.vue";
import SubscriptionEventsTable from "../../../components/app/Subscription/SubscriptionEventsTable.vue";
import SubscriptionMetadata from "../../../components/app/Subscription/SubscriptionMetadata.vue";
import { useSubscriptionApi } from "../../../composables/useSubscriptionApi.js";
import { useModal } from "../../../composables/useModal.js";

export default {
  name: "SubscriptionView",
  components: {
    Currency, 
    RoleOnlyView, 
    VueFinalModal,
    SubscriptionDetails,
    SubscriptionPricing,
    SubscriptionPaymentMethod,
    SubscriptionPaymentsTable,
    SubscriptionEventsTable,
    SubscriptionMetadata
  },
  setup() {
    const { 
      loading,
      error,
      subscription,
      customer,
      product,
      paymentDetails,
      payments,
      usageEstimate,
      subscriptionEvents,
      fetchSubscription
    } = useSubscriptionApi()

    const planModal = useModal()
    const priceModal = useModal()
    const seatModal = useModal()
    const cancelModal = useModal()
    const paymentMethodModal = useModal()

    onMounted(() => {
      const subscriptionId = window.location.pathname.split('/').pop()
      fetchSubscription(subscriptionId)
    })

    return {
      // State
      loading,
      error,
      subscription,
      customer,
      product,
      paymentDetails,
      payments,
      usageEstimate,
      subscriptionEvents,
      
      // Modals
      planModal,
      priceModal,
      seatModal,
      cancelModal,
      paymentMethodModal,
      
      // Additional component state (to be refactored further)
      cancelValues: reactive({
        when: "end-of-run",
        refundType: "none", 
        date: null,
        errors: {},
        cancelled: false
      }),
      planErrors: ref({}),
      planWhen: ref(null),
      planSending: ref(false),
      planReady: ref(false),
      newPlan: ref({}),
      newPrice: ref({id: null}),
      priceSending: ref(false),
      priceReady: ref(false),
      paymentMethodReady: ref(false),
      paymentMethods: ref([]),
      newPaymentMethod: ref({}),
      paymentMethodsSending: ref(false),
      seatSending: ref(false),
      cancelSending: ref(false),
      errorMessage: ref(undefined)
    }
  }
}
</script>

<style scoped>
</style>
