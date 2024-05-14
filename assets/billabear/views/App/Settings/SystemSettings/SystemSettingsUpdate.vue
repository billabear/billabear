<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.system_settings.update.title') }}</h1>

    <LoadingScreen :ready="ready">
      <form @submit.prevent="save">
        <div class="m-5 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="system_url">
              {{ $t('app.settings.system_settings.update.fields.system_url') }}
            </label>
            <p class="form-field-error" v-if="errors.systemUrl != undefined">{{ errors.systemUrl }}</p>
            <input type="text" class="form-field" id="system_url" v-model="systemSettings.system_url"  />
            <p class="form-field-help">{{ $t('app.settings.system_settings.update.help_info.system_url') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="timezone">
              {{ $t('app.settings.system_settings.update.fields.timezone') }}
            </label>
            <p class="form-field-error" v-if="errors.timezone != undefined">{{ errors.timezone }}</p>
            <select class="form-field" id="timezone" v-model="systemSettings.timezone">
              <option v-for="timezone in timezones">{{ timezone }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.settings.system_settings.update.help_info.timezone') }}</p>
          </div>
        </div>

        <div class="m-5 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="invoice_number_generation">
              {{ $t('app.settings.system_settings.update.fields.invoice_number_generation') }}
            </label>
            <p class="form-field-error" v-if="errors.invoiceNumberGeneration != undefined">{{ errors.invoiceNumberGeneration }}</p>
            <select class="form-field" id="timezone" v-model="systemSettings.invoice_number_generation">
              <option value="random">{{ $t('app.settings.system_settings.update.invoice_number_generation.random') }}</option>
              <option value="subsequential">{{ $t('app.settings.system_settings.update.invoice_number_generation.subsequential') }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.settings.system_settings.update.help_info.invoice_number_generation') }}</p>
          </div>

          <div class="form-field-ctn" v-if="systemSettings.invoice_number_generation === 'subsequential'">
            <label class="form-field-lbl" for="subsequential_number">
              {{ $t('app.settings.system_settings.update.fields.subsequential_number') }}
            </label>
            <input type="number" class="form-field" v-model="systemSettings.subsequential_number" />
            <p class="form-field-help">{{ $t('app.settings.system_settings.update.help_info.subsequential_number') }}</p>
          </div>

          <div class="form-field-ctn" >
            <label class="form-field-lbl" for="default_invoice_due_time">
              {{ $t('app.settings.system_settings.update.fields.default_invoice_due_time') }}
            </label>
            <select class="form-field" id="default_invoice_due_time" v-model="systemSettings.default_invoice_due_time">
              <option value="30 days">{{ $t('app.settings.system_settings.update.default_invoice_due_time.30_days') }}</option>
              <option value="60 days">{{ $t('app.settings.system_settings.update.default_invoice_due_time.60_days') }}</option>
              <option value="90 days">{{ $t('app.settings.system_settings.update.default_invoice_due_time.90_days') }}</option>
              <option value="120 days">{{ $t('app.settings.system_settings.update.default_invoice_due_time.120_days') }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.settings.system_settings.update.help_info.default_invoice_due_time') }}</p>
          </div>

        </div>

        <div class="m-5 form-field-submit-ctn">
          <SubmitButton :in-progress="sending">{{ $t('app.settings.system_settings.update.submit_btn') }}</SubmitButton>
        </div>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.system_settings.update.success_message') }}</p>
      </form>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SystemSettingsUpdate",
  data() {
    return {
      sending: false,
      ready: false,
      success: false,
      systemSettings: {},
      errors: {},
      timezones: []
    }
  },
  mounted() {
    axios.get('/app/settings/system').then(response => {
      this.systemSettings = response.data.system_settings;
      this.timezones = response.data.timezones;
      this.ready = true;
    })
  },
  methods: {
    save: function () {
      this.sending = true;
      this.errors = {};
      axios.post('/app/settings/system', this.systemSettings).then(response => {
        this.sending = false;
        this.success = true;
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.sending = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>

</style>