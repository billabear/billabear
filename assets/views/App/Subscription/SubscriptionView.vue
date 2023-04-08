<template>
  <div>
    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="mt-5">
          <h2 class="mb-3">{{ $t('app.subscription.view.title') }}</h2>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.subscription.view.main.status') }}</dt>
              <dd>{{ subscription.status }}</dd>
            </div>

            <div>
              <dt>{{ $t('app.subscription.view.main.plan') }}</dt>
              <dd>
                <router-link :to="{name: 'app.subscription_plan.view', params: {productId: product.id, subscriptionPlanId: subscription.plan.id}}">
                  {{ subscription.plan.name }}
                </router-link>
              </dd>
            </div>
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
          </dl>
        </div>
        <div class="mt-5">
          <h2 class="mb-3">{{ $t('app.subscription.view.pricing.title') }}</h2>
          <dl class="detail-list">
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
        </div>

        <div class="mt-5 text-end">

          <button class="btn--danger" @click="options.modelValue = true">
            {{ $t('app.subscription.view.buttons.cancel') }}
          </button>
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
        <h3 class="mb-4 text-2xl font-semibold">{{ $t('app.subscription.view.modal.cancel.title') }}</h3>
        <div>
          <span class="block text-lg font-medium">{{ $t('app.subscription.view.modal.cancel.when.title') }}</span>
          <select v-model="cancelValues.when" class="form-field">
            <option value="end-of-run">{{ $t('app.subscription.view.modal.cancel.when.end_of_run') }}</option>
            <option value="instantly">{{ $t('app.subscription.view.modal.cancel.when.instantly') }}</option>
            <option value="specific-date">{{ $t('app.subscription.view.modal.cancel.when.specific_date') }}</option>
          </select>
        </div>

        <VueDatePicker  class="mt-2" v-model="cancelValues.date"  :enable-time-picker="false" v-if="cancelValues.when == 'specific-date'"></VueDatePicker>
        <div class="mt-2">

          <span class="block text-lg font-medium">{{ $t('app.subscription.view.modal.cancel.refund_type.title') }}</span>
          <select v-model="cancelValues.refundType"  class="form-field" :disabled="cancelValues.when == 'end-of-run'">
            <option value="none">{{ $t('app.subscription.view.modal.cancel.refund_type.none') }}</option>
            <option value="prorate">{{ $t('app.subscription.view.modal.cancel.refund_type.prorate') }}</option>
            <option value="full">{{ $t('app.subscription.view.modal.cancel.refund_type.full') }}</option>
          </select>
        </div>

        <div class="mt-5 text-center">
          <button class="btn--secondary mr-3" @click="options.modelValue = false">{{ $t('app.subscription.view.modal.cancel.close_btn') }}</button>
          <SubmitButton @click="sendCancel" :in-progress="cancelSending">{{ $t('app.subscription.view.modal.cancel.cancel_btn') }}</SubmitButton>
        </div>
      </div>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import {useModal, VueFinalModal} from "vue-final-modal";

export default {
  name: "SubscriptionView",
  components: {VueFinalModal},
  data() {
    return {
      subscription: {},
      customer: {},
      product: {},
      payments: [],
      refunds: [],
      ready: false,
      error: false,
      errorMessage: undefined,
      cancelValues: {
          when: "end-of-run",
          refundType: "none",
          date: null,
      },
      cancelSending: false,
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
    };
  },
  mounted() {
    var subscriptionId = this.$route.params.subscriptionId
    axios.get('/app/subscription/' + subscriptionId).then(response => {
      this.product = response.data.product;
      this.subscription = response.data.subscription;
      this.customer = response.data.customer;
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
      }).then(response => {
        this.cancelSending = false;
      })
    }
  }
}
</script>

<style scoped>

</style>