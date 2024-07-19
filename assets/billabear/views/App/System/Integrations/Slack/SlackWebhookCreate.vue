<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.system.integrations.slack.webhooks.create.title') }}</h1>

    <div>
      <form @submit.prevent="send">
        <div class="p-5">
          <div class="card-body">

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="name">
                {{ $t('app.system.integrations.slack.webhooks.create.fields.name') }}
              </label>
              <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
              <input type="text" class="form-field-input" id="name" v-model="webhook.name" />
              <p class="form-field-help">{{ $t('app.system.integrations.slack.webhooks.create.help_info.name') }}</p>
            </div>

            <div class="form-field-ctn mt-4">
              <label class="form-field-lbl" for="webhook">
                {{ $t('app.system.integrations.slack.webhooks.create.fields.webhook') }}
              </label>
              <p class="form-field-error" v-if="errors.webhook != undefined">{{ errors.webhook }}</p>
              <input type="text" class="form-field-input" id="webhook" v-model="webhook.webhook" />
              <p class="form-field-help">{{ $t('app.system.integrations.slack.webhooks.create.help_info.webhook') }}</p>
            </div>
          </div>
        </div>
        <SubmitButton class="ml-5" @click="save" :in-progress="inProgress">{{ $t('app.system.integrations.slack.webhooks.create.save_btn') }}</SubmitButton>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SlackWebhookCreate",
  data() {
    return {
      webhook: {
        name: '',
        webhook: '',
      },
      errors: {},
      inProgress: false
    }
  },
  methods: {
    save: function () {
      this.inProgress = true;
      this.errors={};
      axios.post("/app/integrations/slack/webhook/create", this.webhook).then(response => {
        this.inProgress = false;
        this.$router.push({'name': 'app.system.integrations.slack.webhook'})
      }).catch(error => {
        this.inProgress = false;
        this.errors = error.response.data.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>
