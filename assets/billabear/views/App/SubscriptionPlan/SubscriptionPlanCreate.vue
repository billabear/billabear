<template>
  <div>
    <h1 class="page-title">{{ $t('app.subscription_plan.create.title') }}</h1>

    <form @submit.prevent="send">
    <div class="mt-3 card-body">
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.subscription_plan.create.fields.name') }}
        </label>
        <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
        <input type="text" class="form-field-input" id="name" v-model="subscription_plan.name" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.name') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.subscription_plan.create.fields.code_name') }}
        </label>
        <p class="form-field-error" v-if="errors.codeName != undefined">{{ errors.codeName }}</p>
        <input type="text" class="form-field-input" id="name" v-model="subscription_plan.code_name" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.code_name') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="public">
          {{ $t('app.subscription_plan.create.fields.public') }}
        </label>
        <p class="form-field-error" v-if="errors.public != undefined">{{ errors.public }}</p>
        <input type="checkbox" id="public" v-model="subscription_plan.public" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.public') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="per_seat">
          {{ $t('app.subscription_plan.create.fields.per_seat') }}
        </label>
        <p class="form-field-error" v-if="errors.perSeat != undefined">{{ errors.perSeat }}</p>
        <input type="checkbox" id="per_seat" v-model="subscription_plan.per_seat" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.per_seat') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="free">
          {{ $t('app.subscription_plan.create.fields.free') }}
        </label>
        <p class="form-field-error" v-if="errors.free != undefined">{{ errors.free }}</p>
        <input type="checkbox" id="free" v-model="subscription_plan.free" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.free') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="has_trial">
          {{ $t('app.subscription_plan.create.fields.has_trial') }}
        </label>
        <p class="form-field-error" v-if="errors.hasTrial != undefined">{{ errors.hasTrial }}</p>
        <input type="checkbox" id="has_trial" v-model="subscription_plan.has_trial" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.has_trial') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="trial_length_days">
          {{ $t('app.subscription_plan.create.fields.trial_length_days') }}
        </label>
        <p class="form-field-error" v-if="errors.trialLengthDays != undefined">{{ errors.trialLengthDays }}</p>
        <input type="number" class="form-field-input" id="trial_length_days" v-model="subscription_plan.trial_length_days" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.trial_length_days') }}</p>
      </div>

      <div class="form-field-ctn" v-if="subscription_plan.per_seat == false">
        <label class="form-field-lbl" for="user_count">
          {{ $t('app.subscription_plan.create.fields.user_count') }}
        </label>
        <p class="form-field-error" v-if="errors.userCount != undefined">{{ errors.userCount }}</p>
        <input type="number" class="form-field-input" id="user_count" v-model="subscription_plan.user_count" :class="{disabled: subscription_plan.per_seat_plan}" :disabled="subscription_plan.per_seat_plan" />
        <p class="form-field-help">{{ $t('app.subscription_plan.create.help_info.user_count') }}</p>
      </div>
    </div>


      <div class="mt-3 card-body">
        <h2>{{ $t('app.subscription_plan.create.features.title') }}</h2>

        <div v-for="(feature, key) in subscription_plan.features">
          <select class="form-field" v-model="subscription_plan.features[key]">
            <option></option>
            <option v-for="featureInfo in features" :value="featureInfo">{{ featureInfo.name }}</option>
          </select>
          <i class="ml-3 fa-solid fa-trash cursor-pointer" @click="removeFeature(key)"></i>
        </div>
        <button @click.prevent="subscription_plan.features.push({})"  class="mt-5 btn--main">{{ $t('app.subscription_plan.create.features.add_feature') }}</button>
      </div>

      <div class="mt-3 card-body">
        <h2>{{ $t('app.subscription_plan.create.limits.title') }}</h2>

        <div v-for="(limit, key) in subscription_plan.limits">
          <select  class="form-field" v-model="subscription_plan.limits[key].feature">
            <option></option>
            <option v-for="featureInfo in features" :value="featureInfo">{{ featureInfo.name }}</option>
          </select>
          <input type="number" class="form-field" v-model="subscription_plan.limits[key].limit" />
          <i class="ml-3 fa-solid fa-trash cursor-pointer" @click="removeLimit(key)"></i>
        </div>
        <button @click.prevent="subscription_plan.limits.push({feature: {}, limit: 0})" class="mt-5 btn--main">{{ $t('app.subscription_plan.create.limits.add_limit') }}</button>
      </div>


      <div class="mt-3 card-body">
        <h2>{{ $t('app.subscription_plan.create.prices.title') }}</h2>

        <p class="form-field-error" v-if="errors.prices != undefined">{{ errors.prices }}</p>
        <div v-for="(price, key) in subscription_plan.prices">
          <select  class="form-field" v-model="subscription_plan.prices[key]">
            <option></option>
            <option v-for="priceInfo in prices" :value="priceInfo">{{ priceInfo.display_value }} - {{ priceInfo.schedule }}</option>
          </select>
          <i class="ml-3 fa-solid fa-trash cursor-pointer" @click="removePrice(key)"></i>
        </div>
        <button @click.prevent="subscription_plan.prices.push({})" class="mt-5 btn--main">{{ $t('app.subscription_plan.create.prices.add_price') }}</button>
      </div>

    <div class="form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.subscription_plan.create.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.subscription_plan.create.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SubscriptionPlanCreate",
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
        user_count: 1,
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
    this.id = productId;
    axios.get('/app/product/'+productId+'/plan-creation').then(response => {
      this.features = response.data.features;
      this.prices = response.data.prices;
      this.ready = true;
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
  methods: {
    removeFeature: function (key) {
      this.subscription_plan.features.splice(key, 1);
    },
    removeLimit: function (key) {
      this.subscription_plan.limits.splice(key, 1);
    },
    removePrice: function (key) {
      this.subscription_plan.prices.splice(key, 1);
    },
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
      };
      var count = this.subscription_plan.features.length;
      var features = [];
      for (var i = 0; i  < count; i++) {
        if (this.subscription_plan.features[i].id !== undefined && this.subscription_plan.features[i].id !== null) {
          features.push(this.subscription_plan.features[i])
        }
      }
      payload.features = features;

      var count = this.subscription_plan.limits.length;
      var limits = [];
      for (var i = 0; i  < count; i++) {
        if (this.subscription_plan.limits[i].feature !== undefined && this.subscription_plan.limits[i].feature.id !== undefined && this.subscription_plan.limits[i].feature.id !== null &&
            this.subscription_plan.limits[i].limit !== undefined && this.subscription_plan.limits[i].limit !== null) {
          limits.push(this.subscription_plan.limits[i])
        }
      }
      payload.limits = limits;

      var count = this.subscription_plan.prices.length;
      var prices = [];
      for (var i = 0; i  < count; i++) {
        if (this.subscription_plan.prices[i].id !== undefined && this.subscription_plan.prices[i].id !== null) {
          prices.push(this.subscription_plan.prices[i])
        }
      }
      payload.prices = prices;

      axios.post('/app/product/'+productId+'/plan', payload).then(
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