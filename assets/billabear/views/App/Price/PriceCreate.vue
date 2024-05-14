<template>
  <div>
    <h1 class="page-title">{{ $t('app.price.create.title') }}</h1>

    <form @submit.prevent="send">
      <div class="mt-3 card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="amount">
            {{ $t('app.price.create.amount') }}
          </label>
          <p class="form-field-error" v-if="errors.amount != undefined">{{ errors.amount }}</p>
          <input type="number" class="form-field-input" id="amount" v-model="price.amount" />
          <p class="form-field-help">{{ $t('app.price.create.help_info.amount') }}</p>
          <p class="form-field-help">{{ $t('app.price.create.help_info.display_amount', {amount: currency(price.amount)}) }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="currency">
            {{ $t('app.price.create.currency') }}
          </label>
          <p class="form-field-error" v-if="errors.currency != undefined">{{ errors.currency }}</p>
          <CurrencySelect v-model="price.currency" />
          <p class="form-field-help">{{ $t('app.price.create.help_info.currency') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="recurring">
            {{ $t('app.price.create.recurring') }}
          </label>
          <p class="form-field-error" v-if="errors.recurring != undefined">{{ errors.recurring }}</p>
          <input type="checkbox" class="fodsrm-field-input" id="recurring" v-model="price.recurring" />
          <p class="form-field-help">{{ $t('app.price.create.help_info.recurring') }}</p>
        </div>
        <div class="form-field-ctn" v-if="price.recurring">
          <label class="form-field-lbl" for="schedule">
            {{ $t('app.price.create.schedule_label') }}
          </label>
          <p class="form-field-error" v-if="errors.schedule != undefined">{{ errors.schedule }}</p>
          <select class="form-field-input" id="name" v-model="price.schedule">
            <option :value="null"> </option>
            <option value="week">{{ $t('app.price.create.schedule.week') }}</option>
            <option value="month">{{ $t('app.price.create.schedule.month') }}</option>
            <option value="year">{{ $t('app.price.create.schedule.year') }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.price.create.help_info.schedule') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="including_tax">
            {{ $t('app.price.create.including_tax') }}
          </label>
          <p class="form-field-error" v-if="errors.including_tax != undefined">{{ errors.including_tax }}</p>
          <input type="checkbox" class="fodsrm-field-input" id="including_tax" v-model="price.including_tax" />
          <p class="form-field-help">{{ $t('app.price.create.help_info.including_tax') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="public">
            {{ $t('app.price.create.public') }}
          </label>
          <p class="form-field-error" v-if="errors.public != undefined">{{ errors.public }}</p>
          <input type="checkbox" class="fodsrm-field-input" id="public" v-model="price.public" />
          <p class="form-field-help">{{ $t('app.price.create.help_info.public') }}</p>
        </div>
      </div>


      <div class="form-field-ctn">
        <p @click="showAdvance = !showAdvance" class="cursor-pointer">
          <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
          <i class="fa-solid fa-caret-down" v-else></i>
          <span class="ml-2">{{ $t('app.price.create.show_advanced') }}</span>
        </p>
      </div>
      <div class="card-body mt-5" v-if="showAdvance">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.price.create.external_reference') }}
          </label>
          <p class="form-field-error" v-if="errors.external_reference != undefined">{{ errors.external_reference }}</p>
          <input type="text" class="form-field-input" id="external_reference" v-model="price.external_reference"  />
          <p class="form-field-help">{{ $t('app.price.create.help_info.external_reference') }}</p>
        </div>

      </div>

      <div class="form-field-submit-ctn">
        <SubmitButton :in-progress="sendingInProgress">{{ $t('app.product.create.submit_btn') }}</SubmitButton>
      </div>
      <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.product.create.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import CurrencySelect from "../../../components/app/Forms/CurrencySelect.vue";

export default {
  name: "PriceCreate",
  components: {CurrencySelect},
  data() {
    return {
      price: {
        amount: 0,
        currency: null,
        recurring: true,
        schedule: null,
        external_reference: null,
        including_tax: true,
        public: true,
      },
      errors: {},
      sendingInProgress: false,
      showAdvance: false,
      success: false,
    }
  },
  methods: {
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
    send: function () {
      var productId = this.$route.params.productId
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      axios.post('/app/product/' + productId + '/price', this.price).then(
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

</style>