<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.system_settings.update.title') }}</h1>

    <LoadingScreen :ready="ready">
      <form @submit.prevent="save">
        <div class="mt-3 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="webhook_url">
              {{ $t('app.settings.system_settings.update.fields.webhook_url') }}
            </label>
            <p class="form-field-error" v-if="errors.webhookUrl != undefined">{{ errors.webhookUrl }}</p>
            <input type="text" class="form-field" id="webhook_url" v-model="systemSettings.webhook_url"  />
            <p class="form-field-help">{{ $t('app.settings.system_settings.update.help_info.webhook_url') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="timezone">
              {{ $t('app.settings.system_settings.update.fields.default_outgoing_email') }}
            </label>
            <p class="form-field-error" v-if="errors.timezone != undefined">{{ errors.timezone }}</p>
            <select class="form-field" id="timezone" v-model="systemSettings.timezone">
              <option v-for="timezone in timezones">{{ timezone }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.settings.system_settings.update.help_info.timezone') }}</p>
          </div>


        </div>

        <div class="form-field-submit-ctn">
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