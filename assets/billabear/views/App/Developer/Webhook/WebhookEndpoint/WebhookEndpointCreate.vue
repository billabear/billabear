<template>
  <div>
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.system.webhooks.webhook_endpoint.create.title') }}</h1>

    <form @submit.prevent="send">
      <div class="m-5 card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.system.webhooks.webhook_endpoint.create.fields.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field-input" id="name" v-model="endpoint.name" />
          <p class="form-field-help">{{ $t('app.system.webhooks.webhook_endpoint.create.help_info.name') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.system.webhooks.webhook_endpoint.create.fields.url') }}
          </label>
          <p class="form-field-error" v-if="errors.url != undefined">{{ errors.url }}</p>
          <input type="text" class="form-field-input" id="url" v-model="endpoint.url" />
          <p class="form-field-help">{{ $t('app.system.webhooks.webhook_endpoint.create.help_info.url') }}</p>
        </div>
      </div>
      <div class="m-5">
        <SubmitButton :in-progress="inProgress">{{ $t('app.system.webhooks.webhook_endpoint.create.create_button') }}</SubmitButton>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "WebhookEndpointCreate",
  data() {
    return {
      endpoint: {
        name: null,
        url: null,
      },
      inProgress: false,
      errors: {}
    }
  },
  methods: {
    send: function () {
      this.inProgress = true;
      this.errors = {};
      axios.post("/app/developer/webhook", this.endpoint).then(response => {
        this.$router.push({name: "app.system.webhook_endpoints.view", params: {id: response.data.id}})
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.inProgress = false;
      })
    }
  }
}
</script>