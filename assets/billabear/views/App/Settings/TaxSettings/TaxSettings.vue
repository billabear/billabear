<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.tax_settings.update.title') }}</h1>

    <LoadingScreen :ready="ready">

      <form @submit.prevent="save">
        <div class="m-5 card-body">
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
        </div>

      <div class="m-5 form-field-submit-ctn">
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