<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.system.integrations.slack.notifications.create.title') }}</h1>

    <div>
      <form @submit.prevent="send">
        <div class="">
          <div class="card-body">

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="name">
                {{ $t('app.system.integrations.slack.notifications.create.fields.event') }}
              </label>
              <p class="form-field-error" v-if="errors.event != undefined">{{ errors.event }}</p>
              <select class="form-field" id="event"  @change="changeEvent" v-model="webhook.event">
                <option>customer_created</option>
                <option>payment_processed</option>
                <option>subscription_created</option>
                <option>subscription_cancelled</option>
                <option>trial_started</option>
                <option>trial_ended</option>
                <option>trial_converted</option>
                <option>tax_country_threshold_reached</option>
                <option>tax_state_threshold_reached</option>
              </select>
              <p class="form-field-help">{{ $t('app.system.integrations.slack.notifications.create.help_info.event') }}</p>
            </div>

            <div class="form-field-ctn mt-4">
              <label class="form-field-lbl" for="webhook">
                {{ $t('app.system.integrations.slack.notifications.create.fields.webhook') }}
              </label>
              <p class="form-field-error" v-if="errors.webhook != undefined">{{ errors.webhook }}</p>
              <select class="form-field" id="webhook" v-model="webhook.webhook">
                <option v-for="webhook in webhooks" :value="webhook.id">{{ webhook.name }}</option>
              </select>
              <p class="form-field-help">{{ $t('app.system.integrations.slack.notifications.create.help_info.webhook') }}</p>
            </div>

            <div class="form-field-ctn mt-4">
              <label class="form-field-lbl" for="webhook">
                {{ $t('app.system.integrations.slack.notifications.create.fields.template') }}
              </label>
              <p class="form-field-error" v-if="errors.template != undefined">{{ errors.template }}</p>
              <textarea cols="90" rows="5" class="form-field" v-model="webhook.template"></textarea>
              <p class="form-field-help" v-html="$t('app.system.integrations.slack.notifications.create.help_info.template')"></p>
            </div>
          </div>
        </div>
        <SubmitButton class="mt-3" @click="save" :in-progress="inProgress">{{ $t('app.system.integrations.slack.webhooks.create.save_btn') }}</SubmitButton>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import {Select, Textarea} from "flowbite-vue";

export default {
  name: "SlackWebhookCreate",
  components: {Textarea, Select},
  data() {
    return {
      webhooks: [],
      webhook: {
        event: '',
        webhook: '',
      },
      errors: {},
      inProgress: false
    }
  },
  mounted() {
    axios.get("/app/integrations/slack/notification/create").then(response => {
      this.webhooks = response.data.webhooks;
    })
  },
  methods: {
    changeEvent: function () {

      if (this.webhook.event == "customer_created") {
        this.webhook.template = "A new customer ({{customer.email}}) has been created";
      } else if (this.webhook.event === "payment_processed") {
        this.webhook.template = "Successfully processed {{payment.amount_formatted}} for {{customer.email}}";
      } else if (this.webhook.event === "payment_failure") {
        this.webhook.template = "Failed to processed {{payment.amount_formatted}} for {{customer.email}}";
      } else if (this.webhook.event === "subscription_created") {
        this.webhook.template = "Successfully subscription {{subscription.plan_name}} for {{customer.email}}";
      } else if (this.webhook.event === "subscription_cancelled") {
        this.webhook.template = "Subscription {{subscription.plan_name}} for {{customer.email}} cancelled";
      } else if (this.webhook.event === "trial_started") {
        this.webhook.template = "Trial started for {{customer.email}} for {{subscription.plan_name}}";
      } else if (this.webhook.event === "trial_ended") {
        this.webhook.template = "Trial ended for {{customer.email}} for {{subscription.plan_name}}";
      } else if (this.webhook.event === "trial converted") {
        this.webhook.template = "Trial converted into a subscription for {{customer.email}} for {{subscription.plan_name}}";
      } else if (this.webhook.event === "tax_country_threshold_reached") {
        this.webhook.template = "Tax country threshold of {{country.threshold}} reached for {{country.name}}";
      } else if (this.webhook.event === "tax_state_threshold_reached") {
        this.webhook.template = "Tax state threshold of {{state.threshold}} reached for {{ state.name }} in {{country.name}}";
      }
    },
    save: function () {
      this.inProgress = true;
      this.errors={};
      axios.post("/app/integrations/slack/notification/create", this.webhook).then(response => {
        this.inProgress = false;
        this.$router.push({'name': 'app.system.integrations.slack.notification'})
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
