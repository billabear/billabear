<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.notification_settings.update.title') }}</h1>

    <LoadingScreen :ready="ready">

      <form @submit.prevent="save">
        <div class="m-5 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="send_customer_notifications">
              {{ $t('app.settings.notification_settings.update.fields.send_customer_notifications') }}
            </label>
            <p class="form-field-error" v-if="errors.sendCustomerNotifications != undefined">{{ errors.sendCustomerNotifications }}</p>

            <Toggle v-model="notificationSettings.send_customer_notifications" />
            <p class="form-field-help">{{ $t('app.settings.notification_settings.update.help_info.send_customer_notifications') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="default_outgoing_email">
              {{ $t('app.settings.notification_settings.update.fields.default_outgoing_email') }}
            </label>
            <p class="form-field-error" v-if="errors.defaultOutgoingEmail != undefined">{{ errors.defaultOutgoingEmail }}</p>
            <input type="text" class="form-field" id="default_outgoing_email" v-model="notificationSettings.default_outgoing_email"  />
            <p class="form-field-help">{{ $t('app.settings.notification_settings.update.help_info.default_outgoing_email') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="emsp">
              {{ $t('app.settings.notification_settings.update.fields.emsp') }}
            </label>
            <p class="form-field-error" v-if="errors.emsp != undefined">{{ errors.emsp }}</p>
            <select class="form-field" v-model="notificationSettings.emsp">
                <option v-for="emsp in emspChoices">{{ emsp }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.settings.notification_settings.update.help_info.emsp') }}</p>
          </div>

          <div class="form-field-ctn" v-if="notificationSettings.emsp != 'system'">
            <label class="form-field-lbl" for="emsp_api_key">
              {{ $t('app.settings.notification_settings.update.fields.emsp_api_key') }}
            </label>
            <p class="form-field-error" v-if="errors.emsp_api_key != undefined">{{ errors.emsp_api_key }}</p>
            <input type="text" class="form-field-input" id="emsp_api_key" v-model="notificationSettings.emsp_api_key"  />
            <p class="form-field-help">{{ $t('app.settings.notification_settings.update.help_info.emsp_api_key') }}</p>
          </div>
          <div class="form-field-ctn" v-if="notificationSettings.emsp == 'mailgun'">
            <label class="form-field-lbl" for="emsp_api_url">
              {{ $t('app.settings.notification_settings.update.fields.emsp_api_url') }}
            </label>
            <p class="form-field-error" v-if="errors.emsp_api_url != undefined">{{ errors.emsp_api_url }}</p>
            <input type="text" class="form-field-input" id="emsp_api_url" v-model="notificationSettings.emsp_api_url"  />
            <p class="form-field-help">{{ $t('app.settings.notification_settings.update.help_info.emsp_api_url') }}</p>
          </div>
          <div class="form-field-ctn" v-if="notificationSettings.emsp == 'mailgun'">
            <label class="form-field-lbl" for="emsp_domain">
              {{ $t('app.settings.notification_settings.update.fields.emsp_domain') }}
            </label>
            <p class="form-field-error" v-if="errors.emsp_domain != undefined">{{ errors.emsp_domain }}</p>
            <input type="text" class="form-field-input" id="emsp_domain" v-model="notificationSettings.emsp_domain"  />
            <p class="form-field-help">{{ $t('app.settings.notification_settings.update.help_info.emsp_domain') }}</p>
          </div>

        </div>

        <div class="m-tform-field-submit-ctn">
          <SubmitButton :in-progress="sending">{{ $t('app.settings.notification_settings.update.submit_btn') }}</SubmitButton>
        </div>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.notification_settings.update.success_message') }}</p>
      </form>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import {Toggle} from "flowbite-vue";

export default {
  name: "NotificationSettingsUpdate",
  components: {Toggle},
  data() {
    return {
      sending: false,
      ready: false,
      success: false,
      notificationSettings: {},
      errors: {},
      emspChoices: []
    }
  },
  mounted() {
    axios.get('/app/settings/notification-settings').then(response => {
      this.notificationSettings = response.data.notification_settings;
      this.emspChoices = response.data.emsp_choices;
      this.ready = true;
    })
  },
  methods: {
    save: function () {
      this.sending = true;
      this.errors = {};
      axios.post('/app/settings/notification-settings', this.notificationSettings).then(response => {
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