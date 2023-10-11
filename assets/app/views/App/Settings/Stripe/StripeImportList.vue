<template>
  <div>
    <h2 class="page-title">{{ $t('app.settings.stripe.main.title') }}</h2>

    <div class="m-5 text-end">
      <SubmitButton :in-progress="sendingRequest" @click="createImportRequest">{{ $t('app.settings.stripe.main.start_button') }}</SubmitButton>
    </div>

    <LoadingScreen :ready="ready">
      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.settings.stripe.main.list.state') }}</th>
            <th>{{ $t('app.settings.stripe.main.list.last_id')}}</th>
            <th>{{ $t('app.settings.stripe.main.list.created_at') }}</th>
            <th>{{ $t('app.settings.stripe.main.list.updated_at') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="request in importRequests">
              <td>{{ request.state }}</td>
              <td>{{ request.last_id }}</td>
              <td>{{ request.created_at }}</td>
              <td>{{ request.updated_at }}</td>
              <td><router-link :to="{name: 'app.settings.import.stripe.view', params: {id: request.id}}">{{ $t('app.settings.stripe.main.list.view') }}</router-link></td>
            </tr>
            <tr v-if="importRequests.length === 0">
              <td colspan="5" class="text-center">{{ $t('app.settings.stripe.main.list.no_results') }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="m-5 grid grid-cols-2 gap-5">

        <div class="card-body mt-5">

          <h2>{{ $t('app.settings.stripe.main.webhook.title') }}</h2>
          <div class="">

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="country">
                {{ $t('app.settings.stripe.main.webhook.url') }}
              </label>
              <div v-if="!webhook_url_registered">
                <p class="form-field-error" v-if="errors.url != undefined">{{ errors.url }}</p>
                <input type="text" v-model="webhook_url" class="form-field" />
                <SubmitButton :in-progress="sendingWebhookRequest" class="btn--main"  @click="registerWebhook">{{ $t('app.settings.stripe.main.webhook.register_webhook') }}</SubmitButton>

                <p class="form-field-help">{{ $t('app.settings.stripe.main.webhook.help_info.url') }}</p>
              </div>
              <SubmitButton :in-progress="sendingWebhookRequest" class="btn--danger" v-else @click="deregisterWebhook">{{ $t('app.settings.stripe.main.webhook.deregister_webhook') }}</SubmitButton>


            </div>
          </div>
        </div>
        <div class="card-body mt-5">
          <h2>{{ $t('app.settings.stripe.main.danger_zone.title') }}</h2>
          <div class="mt-3">

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="country">
                {{ $t('app.settings.stripe.main.danger_zone.use_stripe_billing') }}
              </label>

              <button class="btn--danger" @click="options.modelValue = true" v-if="use_stripe_billing">{{ $t('app.settings.stripe.main.danger_zone.disable_billing') }}</button>
              <button class="btn--main" v-else @click="enableStripeBilling">{{ $t('app.settings.stripe.main.danger_zone.enable_billing') }}</button>
            </div>
          </div>
        </div>
      </div>
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
      <h1 class="text-center mb-3">
        {{ $t('app.settings.stripe.main.disable_billing_modal.title') }}
      </h1>
      <p>{{ $t('app.settings.stripe.main.disable_billing_modal.disable_all_subscriptions') }}</p>
      <p>{{ $t('app.settings.stripe.main.disable_billing_modal.warning') }}</p>
      <div class="text-center">
        <SubmitButton :in-progress="sendBillingRequest" class="btn--secondary" @click="options.modelValue = false">{{ $t('app.settings.stripe.main.disable_billing_modal.cancel') }}</SubmitButton>
        <SubmitButton :in-progress="sendBillingRequest" class="btn--danger ml-3" @click="confirmStripeBillingDisable">{{ $t('app.settings.stripe.main.disable_billing_modal.confirm') }}</SubmitButton>
      </div>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import {VueFinalModal} from "vue-final-modal";
import {mapActions} from "vuex";

export default {
  name: "StripeImportList",
  components: {VueFinalModal},
  data() {
    return {
      ready: false,
      sendingRequest: false,
      sendingWebhookRequest: false,
      sendBillingRequest: false,
      importRequests: [],
      use_stripe_billing: false,
      webhook_url: null,
      webhook_url_registered: false,
      errors: {},
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
    ...mapActions('onboardingStore', ['stripeImport']),
    registerWebhook: function () {
      this.sendingWebhookRequest = true;
      axios.post('/app/settings/stripe/webhook/register', {url: this.webhook_url}).then(response => {
        this.webhook_url_registered = true;
        this.sendingWebhookRequest = false;
      }).catch(error => {

        this.errors = error.response.data.errors;
        this.sendingWebhookRequest = false;
      })
    },
    deregisterWebhook: function () {
      this.sendingWebhookRequest = true;
      axios.post('/app/settings/stripe/webhook/deregister').then(response => {
        this.webhook_url_registered = false;
        this.sendingWebhookRequest = false;
      }).catch(error => {

        this.sendingWebhookRequest = false;
      })
    },
    createImportRequest: function () {
      this.sendingRequest = true;
      axios.post('/app/settings/stripe-import/start').then(response => {
        this.importRequests.push(response.data);
        this.sendingRequest = false;
        this.stripeImport();
      }).catch(error => {
        this.sendingRequest = false;
        if (error.response.status == 409) {
          alert(this.$t('app.settings.stripe_import.main.already_in_progress'))
        }
      })
    },
    confirmStripeBillingDisable: function () {
      this.sendBillingRequest = true;
      axios.post('/app/settings/stripe/disable-billing').then(response => {
        this.options.modelValue = false;
        this.use_stripe_billing = false;
        this.sendBillingRequest = false;
      })
    },
    enableStripeBilling: function () {
      this.sendBillingRequest = true;
      axios.post('/app/settings/stripe/enable-billing').then(response => {
        this.sendBillingRequest = false;
        this.use_stripe_billing = true;
      })
    }
  },
  mounted() {
    axios.get('/app/settings/stripe-import').then(response => {

      this.importRequests = response.data.stripe_imports;
      this.use_stripe_billing = response.data.use_stripe_billing;
      if (response.data.webhook_url) {
        this.webhook_url_registered = true;
        this.webhook_url = response.data.webhook_url;
      } else {
        this.webhook_url_registered = false;
        this.webhook_url = window.location.origin + "/webhook";
      }
      this.ready = true;
    }).catch(error => {

    })
  }
}
</script>

<style scoped>

</style>