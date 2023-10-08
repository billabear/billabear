<template>
  <div>
    <h1 class="page-title">{{ $t('app.workflows.subscription_creation.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mx-5 grid grid-cols-2 gap-5">

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.subscription_creation.view.subscription.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.subscription_creation.view.subscription.name') }}</dt>
              <dd>{{ subscription_creation.subscription.plan.name }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.workflows.subscription_creation.view.subscription.customer') }}</dt>
              <dd><router-link :to="{name: 'app.customer.view', params: {id: subscription_creation.subscription.customer.id}}">{{ subscription_creation.subscription.customer.email }}</router-link></dd>
            </div>
          </dl>
          <router-link :to="{name: 'app.subscription.view', params: {subscriptionId: subscription_creation.subscription.id}}" class="btn--container">{{ $t('app.workflows.subscription_creation.view.subscription.view') }}</router-link>
        </div>

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.subscription_creation.view.details.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.subscription_creation.view.details.state') }}</dt>
              <dd>{{ subscription_creation.state }}</dd>
            </div>
          </dl>
        </div>
      </div>
      <div class="card-body m-5" v-if="subscription_creation.has_error">
        <div class="section-header">{{ $t('app.workflows.subscription_creation.view.error.title') }}</div>
        <pre>{{ subscription_creation.error }}</pre>
      </div>
      <div class="m-5">
        <SubmitButton :in-progress="sending" class="btn--secondary" @click="callProcess" v-if="subscription_creation.state !== 'completed'">{{ $t('app.workflows.subscription_creation.view.buttons.process') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SubscriptionCreationView",
  data() {
    return {
      ready: false,
      subscription_creation: null,
      sending: false,
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/system/subscription-creation/'+id+'/view').then(response => {
      this.subscription_creation = response.data.subscription_creation;
      this.ready = true;
    })
  },
  methods: {
    callProcess: function () {
      const id = this.$route.params.id;
      this.sending = true;
      axios.post('/app/system/subscription-creation/'+id+'/process').then(response => {
        this.subscription_creation = response.data;
        this.sending = false;
      })
    }
  }
}
</script>

<style scoped>
pre {
  @apply whitespace-pre-wrap p-3 rounded bg-gray-50 dark:bg-gray-700 dark:text-white;
}
</style>