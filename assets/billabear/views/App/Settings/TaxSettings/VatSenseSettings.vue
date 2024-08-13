<template>
  <div>
    <PageTitle>{{ $t('app.settings.tax_settings.vatsense.title') }}</PageTitle>

    <div class="my-3 p-3 card-body">
      <img src="/images/vatsense-logo.png" alt="VatSense API" />
      <p class="pt-3">{{ $t('app.settings.tax_settings.vatsense.description') }}</p>

      <p class="py-3">{{ $t('app.settings.tax_settings.vatsense.create_account') }}</p>
      <a class="mt-5 btn--main" href="https://vatsense.com/signup?referral=BILLABEAR" target="_blank">{{ $t('app.settings.tax_settings.vatsense.create_account_link') }}</a>
     </div>

    <LoadingScreen :ready="ready">

      <form @submit.prevent="save">
        <div class="card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="vat_sense_enabled">
              {{ $t('app.settings.tax_settings.vatsense.fields.vat_sense_enabled') }}
            </label>
            <p class="form-field-error" v-if="errors.vatSenseEnabled != undefined">{{ errors.vatSenseEnabled }}</p>
            <Toggle v-model="tax_settings.vat_sense_enabled" />
            <p class="form-field-help">{{ $t('app.settings.tax_settings.vatsense.help_info.vat_sense_enabled') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="vat_sense_api_key">
              {{ $t('app.settings.tax_settings.vatsense.fields.vat_sense_api_key') }}
            </label>
            <p class="form-field-error" v-if="errors.vatSenseApiKey != undefined">{{ errors.vatSenseApiKey }}</p>
            <input type="text" class="form-field" v-model="tax_settings.vat_sense_api_key" />
            <p class="form-field-help" v-html="$t('app.settings.tax_settings.vatsense.help_info.vat_sense_api_key')"></p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="vat_sense_enabled">
              {{ $t('app.settings.tax_settings.vatsense.fields.validate_vat_ids') }}
            </label>
            <p class="form-field-error" v-if="errors.validateVatIds != undefined">{{ errors.validateVatIds }}</p>
            <Toggle v-model="tax_settings.validate_vat_ids" />
            <p class="form-field-help">{{ $t('app.settings.tax_settings.vatsense.help_info.validate_vat_ids') }}</p>
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
import PageTitle from "../../../../components/app/Ui/Typography/PageTitle.vue";
import axios from "axios";
import {Toggle} from "flowbite-vue";

export default {
  name: "VatSenseSettings",
  components: {Toggle, PageTitle},
  data() {
    return {
      ready: false,
      sending: false,
      tax_settings: {},
      errors: {}
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
      axios.post("/app/settings/tax/vatsense", this.tax_settings).then(response => {
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
