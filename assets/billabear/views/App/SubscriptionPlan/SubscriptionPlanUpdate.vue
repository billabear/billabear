<template>
  <div>
    <h1 class="page-title">{{ $t('app.subscription_plan.update.title') }}</h1>

    <form @submit.prevent="send">
      <div class="grid grid-cols-2 gap-4">
        <div class="card-body">
          <h2 class="section-header">{{ $t('app.subscription_plan.create.main_section.title') }}</h2>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription_plan.create.main_section.fields.name') }}
            </label>
            <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
            <input type="text" class="form-field" id="name" v-model="subscription_plan.name" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.main_section.help_info.name') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.subscription_plan.create.main_section.fields.code_name') }}
            </label>
            <p class="form-field-error" v-if="errors.codeName != undefined">{{ errors.codeName }}</p>
            <input type="text" class="form-field" id="name" v-model="subscription_plan.code_name" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.main_section.help_info.code_name') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="public">
              {{ $t('app.subscription_plan.create.main_section.fields.public') }}
            </label>
            <p class="form-field-error" v-if="errors.public != undefined">{{ errors.public }}</p>
            <input type="checkbox" id="public" v-model="subscription_plan.public" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.main_section.help_info.public') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="per_seat">
              {{ $t('app.subscription_plan.create.main_section.fields.per_seat') }}
            </label>
            <p class="form-field-error" v-if="errors.perSeat != undefined">{{ errors.perSeat }}</p>
            <input type="checkbox" id="per_seat" v-model="subscription_plan.per_seat" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.main_section.help_info.per_seat') }}</p>
          </div>


          <div class="form-field-ctn">
            <label class="form-field-lbl" for="free">
              {{ $t('app.subscription_plan.create.main_section.fields.free') }}
            </label>
            <p class="form-field-error" v-if="errors.free != undefined">{{ errors.free }}</p>
            <input type="checkbox" id="free" v-model="subscription_plan.free" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.main_section.help_info.free') }}</p>
          </div>
        </div>

        <div class="card-body">
          <h2 class="section-header">{{ $t('app.subscription_plan.create.trial_section.title') }}</h2>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="has_trial">
              {{ $t('app.subscription_plan.create.trial_section.fields.has_trial') }}
            </label>
            <p class="form-field-error" v-if="errors.hasTrial != undefined">{{ errors.hasTrial }}</p>
            <input type="checkbox" id="has_trial" v-model="subscription_plan.has_trial" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.trial_section.help_info.has_trial') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="has_trial">
              {{ $t('app.subscription_plan.create.trial_section.fields.is_trial_standalone') }}
            </label>
            <p class="form-field-error" v-if="errors.isTrialStandalone != undefined">{{ errors.isTrialStandalone }}</p>
            <input type="checkbox" id="has_trial" v-model="subscription_plan.is_trial_standalone" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.trial_section.help_info.is_trial_standalone') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="trial_length_days">
              {{ $t('app.subscription_plan.create.trial_section.fields.trial_length_days') }}
            </label>
            <p class="form-field-error" v-if="errors.trialLengthDays != undefined">{{ errors.trialLengthDays }}</p>
            <input type="number" class="form-field" id="trial_length_days" v-model="subscription_plan.trial_length_days" />
            <p class="form-field-help">{{ $t('app.subscription_plan.create.trial_section.help_info.trial_length_days') }}</p>
          </div>
        </div>


      <SectionFeatures />
      <SectionLimits />
      <SectionPrices />
      </div>
    <div class="ml-5 form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.subscription_plan.update.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.subscription_plan.update.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import {mapActions, mapState} from "vuex";
import SectionLimits from "./Parts/SectionLimits.vue";
import SectionFeatures from "./Parts/SectionFeatures.vue";
import SectionPrices from "./Parts/SectionPrices.vue";

export default {
  name: "SubscriptionPlanUpdate",
  components: {SectionPrices, SectionFeatures, SectionLimits},
  data() {
    return {
      subscription_plan: {
        name: null,
        code_name: null,
        external_reference: null,
        per_seat: false,
        free: false,
        public: true,
        prices: [{}],
        limits: [{}],
        features: [{}],
        has_trial: false,
        trial_length_days: 0,
        user_count: 1,
        is_trial_standalone: false,
      },
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      errors: {
      },
      product: {},
      prices: [],
      features: []
    }
  },
  mounted() {
    var productId = this.$route.params.productId
    var subscriptionPlanId = this.$route.params.subscriptionPlanId;
    this.fetchSubscriptionPlan({productId, subscriptionPlanId}).then(response => {
      this.subscription_plan = response.data.subscription_plan;
    }).catch(error => {
      if (error.response.status == 404) {
        this.errorMessage = this.$t('app.product.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.product.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  },
  computed: {
      ...mapState('planStore', ['selectedFeatures', 'selectedLimits', 'selectedPrices'])
  },
  methods: {
    ...mapActions('planStore', ['fetchSubscriptionPlan']),
    send: function () {
      var productId = this.$route.params.productId
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      var codeName = null;
      if (codeName !== undefined && this.subscription_plan.code_name !== '') {
        codeName = this.subscription_plan.code_name;
      }
      var payload = {
        name: this.subscription_plan.name,
        code_name: codeName,
        free: this.subscription_plan.free,
        per_seat: this.subscription_plan.per_seat,
        user_count: this.subscription_plan.user_count,
        public: this.subscription_plan.public,
        features: [],
        limits: [],
        prices: [],
        has_trial: this.subscription_plan.has_trial,
        trial_length_days: this.subscription_plan.trial_length_days,
        is_trial_standalone: this.subscription_plan.is_trial_standalone,
      };

      var count = this.selectedFeatures.length;
      var features = [];
      for (var i = 0; i  < count; i++) {
        if (this.selectedFeatures[i].id !== undefined && this.selectedFeatures[i].id !== null) {
          features.push(this.selectedFeatures[i])
        }
      }
      payload.features = features;

      var count = this.selectedLimits.length;
      var limits = [];
      for (var i = 0; i  < count; i++) {
        if (this.selectedLimits[i].feature !== undefined && this.selectedLimits[i].feature.id !== undefined && this.selectedLimits[i].feature.id !== null &&
            this.selectedLimits[i].limit !== undefined && this.selectedLimits[i].limit !== null) {
          limits.push(this.selectedLimits[i])
        }
      }
      payload.limits = limits;

      var count = this.selectedPrices.length;
      var prices = [];
      for (var i = 0; i  < count; i++) {
        if (this.selectedPrices[i].id !== undefined && this.selectedPrices[i].id !== null) {
          prices.push(this.selectedPrices[i])
        }
      }
      payload.prices = prices;

      var subscriptionPlanId = this.$route.params.subscriptionPlanId;
      axios.post('/app/product/'+productId+'/plan/'+subscriptionPlanId + '/update', payload).then(
          response => {
            this.sendingInProgress = false;
            this.success = true;
          }
      ).catch(error => {
        this.errors = error.response.data.errors;
        this.sendingInProgress = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>
.form-field-error {
  @apply text-red-500 text-xs italic mb-2;
}

.form-field-ctn {
  @apply w-full md:w-1/2 px-3 mb-6 md:mb-0 pt-2;
}

.form-field-lbl {
  @apply block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2;
}

.form-field-input {
  @apply appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white;
}

.form-field-help {
  @apply text-gray-600 text-xs italic;
}

.form-field-submit-ctn {
  @apply mt-3;
}
</style>
