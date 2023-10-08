<template>
  <div>
    <h1 class="page-title">{{ $t('app.workflows.cancellation_request.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mx-5 grid grid-cols-2 gap-5">

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.cancellation_request.view.subscription.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.cancellation_request.view.subscription.name') }}</dt>
              <dd>{{ cancellation_request.subscription.plan.name }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.workflows.cancellation_request.view.subscription.customer') }}</dt>
              <dd><router-link :to="{name: 'app.customer.view', params: {id: cancellation_request.subscription.customer.id}}">{{ cancellation_request.subscription.customer.email }}</router-link></dd>
            </div>
            <div>
              <dt>{{ $t('app.workflows.cancellation_request.view.subscription.original_cancellation_date') }}</dt>
              <dd>{{ $filters.moment(cancellation_request.original_valid_until, 'LL') }}</dd>
            </div>
          </dl>
        </div>

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.cancellation_request.view.details.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.cancellation_request.view.details.state') }}</dt>
              <dd>{{ cancellation_request.state }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.workflows.cancellation_request.view.details.when') }}</dt>
              <dd>{{ cancellation_request.when }}</dd>
            </div>
            <div v-if="cancellation_request.when == 'specific-date'">
              <dt>{{ $t('app.workflows.cancellation_request.view.details.specific_date') }}</dt>
              <dd>{{ $filters.moment(cancellation_request.specific_date, 'LL')}}</dd>
            </div>
            <div>
              <dt>{{ $t('app.workflows.cancellation_request.view.details.refund_type') }}</dt>
              <dd>{{ cancellation_request.refund_type }}</dd>
            </div>
          </dl>
        </div>
      </div>
      <div class="card-body m-5" v-if="cancellation_request.has_error">
        <div class="section-header">{{ $t('app.workflows.cancellation_request.view.error.title') }}</div>
        <pre>{{ cancellation_request.error }}</pre>
      </div>
      <div class="m-5">
        <SubmitButton :in-progress="sending" class="btn--secondary" @click="callProcess" v-if="cancellation_request.state !== 'completed'">{{ $t('app.workflows.cancellation_request.view.buttons.process') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CancellationRequestView",
  data() {
    return {
      ready: false,
      cancellation_request: null,
      sending: false,
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/system/cancellation-request/'+id+'/view').then(response => {
      this.cancellation_request = response.data.cancellation_request;
      this.ready = true;
    })
  },
  methods: {
    callProcess: function () {
      const id = this.$route.params.id;
      this.sending = true;
      axios.post('/app/system/cancellation-request/'+id+'/process').then(response => {
        this.cancellation_request = response.data;
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