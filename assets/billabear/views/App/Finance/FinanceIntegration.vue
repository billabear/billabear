<template>
  <div>
    <h1 class="page-title ml-5 mt-5">{{ $t('app.finance.integration.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="card-body">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.finance.integration.fields.integration') }}
          </label>
          <select v-model="integration" class="form-field">
            <option v-for="integration in integrations" :value="integration">{{ integration.name }}</option>
          </select>
        </div>

        <div class="form-field-ctn" v-if="integration!== null && integration.authentication_type == 'api_key'">
          <label class="form-field-lbl" for="api_key">
            {{ $t('app.finance.integration.fields.api_key') }}
          </label>
          <input v-model="api_key" class="form-field" type="text" id="api_key" name="api_key">
        </div>
        <div class="form-field-ctn mt-3" v-if="integration!== null && integration.authentication_type == 'oauth'">
          <a :href="'/app/'+integration.name+'/oauth/start'" class="btn--main" v-if="enabled == false">{{ $t('app.finance.integration.buttons.connect') }}</a>
          <button class="btn--main" @click="disconnectOauth()">{{ $t('app.finance.integration.buttons.disconnect') }}</button>
        </div>
      </div>

      <div class="card-body mt-3" v-if="integration.settings.length > 0">
        <h2 class="text-2xl">{{ $t('app.finance.integration.settings.title') }}</h2>
        <div class="form-field-ctn" v-for="setting in integration.settings">
          <label class="form-field-lbl">{{ $t(setting.label) }}</label>
          <span class="form-field-error block" v-if="errors[setting.name] != undefined">{{ $t(errors[setting.name]) }}</span>
          <input v-model="setting.value" class="form-field" type="text" v-if="setting.type === 'text'" />
          <Toggle v-model="setting.value" v-if="setting.type === 'checkbox'" />
        </div>

        <SubmitButton :in-progress="send_request" class="btn--main mt-3" @click="saveSettings()">{{ $t('app.finance.integration.buttons.save') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>

</template>

<script>
import axios from "axios";
import {Toggle} from "flowbite-vue";

export default {
  name: "FinanceIntegration",
  components: {Toggle},
  data() {
    return {
      integrations: [],
      enabled: false,
      api_key: '',
      integration_name: '',
      integration: null,
      ready: false,
      settings: {},
      send_request: false,
      errors: {},
    }
  },
  mounted() {
    axios.get('/app/integrations/accounting/settings').then(response => {
      this.integrations = response.data.integrations;
      this.enabled = response.data.enabled;
      this.integration_name = response.data.integration_name;
      this.api_key = response.data.api_key;
      this.settings = response.data.settings;

      for (let i = 0;  i < this.integrations.length; i++) {
        if (this.integrations[i].name === this.integration_name) {
          this.integration = this.integrations[i];
          for (const key in this.settings) {
            if (this.settings.hasOwnProperty(key)) {
              const value = this.settings[key];
              for (let j = 0; j < this.integration.settings.length; j++) {
                if (this.integration.settings[j].name === key) {
                  this.integration.settings[j].value = value;
                }
              }
            }
          }
        }
      }
      this.ready = true;
    })
  },
  methods: {
    disconnectOauth() {
      axios.post('/app/integrations/accounting/disconnect').then(response => {
        this.enabled = false;
      })
    },
    disableIntegration() {
      axios.post('/app/integrations/accounting/disable').then(response => {
        this.enabled = false;
      })
    },
    saveSettings() {
      this.send_request = true;
      let settings = {};
      this.errors = {};
      for (let i = 0; i < this.integration.settings.length; i++) {
        let setting = this.integration.settings[i];
        if ((setting.value === null || setting.value === undefined || setting.value === "") && setting.required === true) {
          this.errors[setting.name] = 'app.finance.integration.errors.required';
          continue;
        }

        settings[setting.name] = setting.value;
      }

      if (Object.keys(this.errors).length !== 0) {
        this.send_request = false;
        return;
      }

      axios.post('/app/integrations/accounting/settings', {settings: settings}).then(response => {
        this.send_request = false;
      })
    }
  }
}
</script>

<style scoped>

</style>
