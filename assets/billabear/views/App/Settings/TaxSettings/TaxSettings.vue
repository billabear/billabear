<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.tax_settings.update.title') }}</h1>

    <LoadingScreen :ready="ready">

      <form @submit.prevent="save">
        <div class="card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="tax_customers_with_tax_number">
              {{ $t('app.settings.tax_settings.update.fields.tax_customers_with_tax_number') }}
            </label>
            <p class="form-field-error" v-if="errors.taxCustomersWithTaxNumber != undefined">{{ errors.taxCustomersWithTaxNumber }}</p>
            <Toggle v-model="tax_settings.tax_customers_with_tax_number" />
            <p class="form-field-help">{{ $t('app.settings.tax_settings.update.help_info.tax_customers_with_tax_number') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="eu_business_tax_rules">
              {{ $t('app.settings.tax_settings.update.fields.eu_business_tax_rules') }}
            </label>
            <p class="form-field-error" v-if="errors.euBusinessTaxRules != undefined">{{ errors.euBusinessTaxRules }}</p>
            <Toggle v-model="tax_settings.eu_business_tax_rules" />
            <p class="form-field-help">{{ $t('app.settings.tax_settings.update.help_info.eu_business_tax_rules') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="eu_business_tax_rules">
              {{ $t('app.settings.tax_settings.update.fields.eu_one_stop_shop_rule') }}
            </label>
            <p class="form-field-error" v-if="errors.euOneStopShopRule != undefined">{{ errors.euOneStopShopRule }}</p>
            <Toggle v-model="tax_settings.eu_one_stop_shop_rule" />
            <p class="form-field-help">{{ $t('app.settings.tax_settings.update.help_info.eu_one_stop_shop_rule') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="vat_sense_enabled">
              {{ $t('app.settings.tax_settings.update.fields.vat_sense_enabled') }}
            </label>
            <p class="form-field-error" v-if="errors.vatSenseEnabled != undefined">{{ errors.vatSenseEnabled }}</p>
            <Toggle v-model="tax_settings.vat_sense_enabled" />
            <p class="form-field-help">{{ $t('app.settings.tax_settings.update.help_info.vat_sense_enabled') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="vat_sense_api_key">
              {{ $t('app.settings.tax_settings.update.fields.vat_sense_api_key') }}
            </label>
            <p class="form-field-error" v-if="errors.vatSenseApiKey != undefined">{{ errors.vatSenseApiKey }}</p>
            <input type="text" class="form-field" v-model="tax_settings.vat_sense_api_key" />
            <p class="form-field-help" v-html="$t('app.settings.tax_settings.update.help_info.vat_sense_api_key')"></p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="vat_sense_enabled">
              {{ $t('app.settings.tax_settings.update.fields.validate_vat_ids') }}
            </label>
            <p class="form-field-error" v-if="errors.validateVatIds != undefined">{{ errors.validateVatIds }}</p>
            <Toggle v-model="tax_settings.validate_vat_ids" />
            <p class="form-field-help">{{ $t('app.settings.tax_settings.update.help_info.validate_vat_ids') }}</p>
          </div>
        </div>

      <div class="mt-3 form-field-submit-ctn">
        <SubmitButton :in-progress="sending">{{ $t('app.settings.tax_settings.update.submit_btn') }}</SubmitButton>
      </div>
      <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.tax_settings.update.success_message') }}</p>
      </form>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import {Toggle} from "flowbite-vue";

export default {
  name: "TaxSettings",
  components: {Toggle},
  data() {
    return {
      ready: false,
      sending_request: false,
      errors: {},
      success: false,
      sending: false,
      tax_settings: {
        tax_customers_with_tax_number: false,
        eu_business_tax_rules: false,
        eu_one_stop_shop_rule: false,
        vat_sense_enabled: false,
        vat_sense_api_key: '',
        validate_vat_ids: false,
      }
    }
  },
  mounted() {
    axios.get("/app/settings/tax").then(response => {
      this.tax_settings = response.data;
      this.ready = true;
    })
  },
  methods: {
    save: function () {
      this.sending = true;
      this.errors = {};
      console.log(this.tax_settings);
      axios.post("/app/settings/tax", this.tax_settings).then(response => {
          this.success = true;
          this.sending = false;
      }).catch(error => {
        this.sending = false;
        if (error.response) {
          this.errors = error.response.data.errors;
        }
      })
    }
  }
}
</script>

<style scoped>
</style>
