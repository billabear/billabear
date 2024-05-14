<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.api_keys.main.title') }}</h1>

    <div class="m-5 text-end">
      <button class="btn--main" @click="options.modelValue = true">{{ $t('app.settings.api_keys.main.add_new_button') }}</button>
    </div>

    <LoadingScreen :ready="ready">

      <table class="list-table">
        <thead>
        <tr>
          <th>{{ $t('app.settings.api_keys.main.list.name') }}</th>
          <th>{{ $t('app.settings.api_keys.main.list.key') }}</th>
          <th>{{ $t('app.settings.api_keys.main.list.expires_at') }}</th>
          <th>{{ $t('app.settings.api_keys.main.list.created_at') }}</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
          <tr v-for="key in apiKeys">
            <td>{{ key.name }}</td>
            <td>{{ key.key }}</td>
            <td>{{ key.expires_at }}</td>
            <td>{{ key.created_at }}</td>
            <td><button v-if="key.active" @click="disable(key)" class="btn--danger">{{ $t('app.settings.api_keys.main.list.disable_button') }}</button></td>
          </tr>
          <tr v-if="apiKeys.length === 0">
            <td colspan="5" class="text-center">{{ $t('app.settings.api_keys.main.list.no_api_keys') }}</td>
          </tr>
        </tbody>
      </table>
    </LoadingScreen>

    <VueFinalModal
        v-model="options.modelValue"
        :teleport-to="options.teleportTo"
        :display-directive="options.displayDirective"
        :hide-overlay="options.hideOverlay"
        :overlay-transition="options.overlayTransition"
        :content-transition="options.contentTransition"
        :click-to-close="options.clickToClose"
        :esc-to-close="options.escToClose"
        :background="options.background"
        :lock-scroll="options.lockScroll"
        :swipe-to-close="options.swipeToClose"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <div>
        <h3 class="mb-4 text-2xl font-semibold">{{ $t('app.settings.api_keys.main.create.title') }}</h3>
        <div>
          <span class="block text-lg font-medium">{{ $t('app.settings.api_keys.main.create.name') }}</span>
          <span class="block font-red" v-if="errors.name != undefined">{{ errors.name }}</span>
          <input type="text" class="form-field" v-model="newApiKey.name" />
        </div>

        <div>
          <span class="block text-lg font-medium">{{ $t('app.settings.api_keys.main.create.expires') }}</span>
        <span class="font-red block" v-if="errors.expiresAt != undefined">{{ errors.expiresAt }}</span>
        <VueDatePicker  class="mt-2" v-model="newApiKey.expires"  :enable-time-picker="false"></VueDatePicker>
        </div>

        <div class="mt-5 text-center">
          <button class="btn--secondary mr-3" @click="options.modelValue = false">{{ $t('app.settings.api_keys.main.create.close') }}</button>
          <SubmitButton @click="createApiKey" :in-progress="sendingInProgress">{{ $t('app.settings.api_keys.main.create.create_button') }}</SubmitButton>
        </div>
      </div>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import {VueFinalModal} from "vue-final-modal";

export default {
  name: "ApiKeysMain",
  components: {VueFinalModal},
  data() {
    return {
      ready: false,
      apiKeys: [],
      errors: {},
      sendingInProgress: false,
      newApiKey: {
        name: null,
        expires: null,
      },
      options: {
        teleportTo: 'body',
        modelValue: false,
        displayDirective: 'if',
        hideOverlay: false,
        overlayTransition: 'vfm-fade',
        contentTransition: 'vfm-fade',
        clickToClose: true,
        escToClose: true,
        background: 'non-interactive',
        lockScroll: true,
        swipeToClose: 'none',
      },
    }
  },
  mounted() {
    axios.get('/app/settings/api-key').then(response => {
      this.apiKeys = response.data.data;
      this.ready = true;
    })
  },
  methods: {
    disable: function (key) {
      axios.post('/app/settings/api-key/'+key.id+'/disable').then(response => {

        key.active = false;
      })
    },
    createApiKey: function () {
      this.sendingInProgress = true;
      const payload = {
        expires_at: this.newApiKey.expires,
        name: this.newApiKey.name,
      }
      axios.post('/app/settings/api-key', payload).then(response => {
        this.sendingInProgress = false;
        this.newApiKey = {}
        this.apiKeys.push(response.data);
        this.options.modelValue = false;
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.sendingInProgress = false;
      })
    }
  }
}
</script>

<style scoped>

</style>