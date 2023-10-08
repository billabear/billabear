<template>
  <div>
    <h1 class="page-title">{{ $t('app.workflows.refund_created_process.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mx-5 grid grid-cols-2 gap-5">

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.refund_created_process.view.refund.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.refund_created_process.view.refund.customer') }}</dt>
              <dd><router-link :to="{name: 'app.customer.view', params: {id: refund_created_process.refund.customer.id}}">{{
                  refund_created_process.refund.customer.email
                }}</router-link></dd>
            </div>
          </dl>
          <router-link :to="{name: 'app.refund.view', params: {id: refund_created_process.refund.id}}" class="btn--container">{{ $t('app.workflows.refund_created_process.view.refund.view') }}</router-link>
        </div>

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.refund_created_process.view.details.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.refund_created_process.view.details.state') }}</dt>
              <dd>{{ refund_created_process.state }}</dd>
            </div>
          </dl>
        </div>
      </div>
      <div class="card-body m-5" v-if="refund_created_process.has_error">
        <div class="section-header">{{ $t('app.workflows.refund_created_process.view.error.title') }}</div>
        <pre>{{ refund_created_process.error }}</pre>
      </div>
      <div class="m-5">
        <SubmitButton :in-progress="sending" class="btn--secondary" @click="callProcess" v-if="refund_created_process.state !== 'completed'">{{ $t('app.workflows.refund_created_process.view.buttons.process') }}</SubmitButton>
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
      refund_created_process: {},
      sending: false,
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/system/refund-created-process/'+id+'/view').then(response => {
      this.refund_created_process = response.data.refund_created_process;
      console.log(response.data.refund_created_process)
      this.ready = true;
    })
  },
  methods: {
    callProcess: function () {
      const id = this.$route.params.id;
      this.sending = true;
      axios.post('/app/system/refund-created-process/'+id+'/process').then(response => {
        this.refund_created_process = response.data;
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