<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.system.webhooks.event.view.title') }}</h1>

    <LoadingScreen :ready="ready" v-if="!error">
      <div class="p-5">

        <div class="card-body">
          <h2 class="section-header">{{ $t('app.system.webhooks.event.view.main.title') }}</h2>
          <div class="section-body">

            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.system.webhooks.event.view.main.type') }}</dt>
                <dd>{{ event.type}}</dd>
              </div>
              <div>
                <dt>{{ $t('app.system.webhooks.event.view.main.created_at') }}</dt>
                <dd>{{ event.created_at }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.system.webhooks.event.view.main.payload') }}</dt>
                <dd>{{ event.payload }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <div class="mt-5">
          <h2>{{ $t('app.system.webhooks.event.view.responses.title') }}</h2>
          <div class="mt-3">
            <table class="list-table">
              <thead>
              <tr>
                <th>{{ $t('app.system.webhooks.event.view.responses.list.url')}}</th>
                <th>{{ $t('app.system.webhooks.event.view.responses.list.created_at')}}</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="response in responses">
                <td>{{ response.url }}</td>
                <td>{{ response.created_at }}</td>
                <td><button @click="showResponse(response)" class="btn--main">{{ $t('app.system.webhooks.event.view.responses.list.view') }}</button></td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </LoadingScreen>
    <div class="error" v-else>
      {{ errorMessage }}
    </div>

    <VueFinalModal
        v-model="viewResponse.modelValue"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <h2>{{ $t('app.system.webhooks.event.view.info.title') }}</h2>

      <div v-if="viewResponse.response.error_message">
        <h4>{{ $t('app.system.webhooks.event.view.info.error_message') }}</h4>
        <pre>{{ viewResponse.response.error_message }}</pre>
      </div>
      <div v-else>

        <h4>{{ $t('app.system.webhooks.event.view.info.status_code') }}</h4>
        <pre>{{ viewResponse.response.status_code }}</pre>
        <h4>{{ $t('app.system.webhooks.event.view.info.body') }}</h4>
        <pre style="max-height: 300px; overflow: scroll">{{ viewResponse.response.body }}</pre>
      </div>

      <h4>{{ $t('app.system.webhooks.event.view.info.processing_time') }}</h4>
      <pre>{{ viewResponse.response.processing_time }}</pre>
    </VueFinalModal>

  </div>
</template>

<script>
import axios from "axios";
import {VueFinalModal} from "vue-final-modal";

export default {
  name: "WebhookEventView",
  components: {VueFinalModal},
  data() {
    return {
      event: {},
      responses: [],
      ready: false,
      error: false,
      errorMessage: false,
      viewResponse: {
        modelValue: false,
        response: {}
      }
    }
  },
  mounted() {
    var id = this.$route.params.id
    axios.get("/app/developer/webhook/event/"+id+"/view").then(response => {
      this.event = response.data.event;
      this.responses = response.data.responses;
      this.ready = true;
    }).catch(error => {
      this.error = true;
      this.errorMessage = this.$t('app.system.webhooks.event.view.error_message')
    })
  },
  methods: {
    showResponse: function (response) {
      this.viewResponse.response = response;
      this.viewResponse.modelValue = true;
    }
  }
}
</script>

<style scoped>

</style>