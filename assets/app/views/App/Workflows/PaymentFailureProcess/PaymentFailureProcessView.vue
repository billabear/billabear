<template>
  <div>
    <h1 class="page-title">{{ $t('app.workflows.payment_failure_process.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mx-5 grid grid-cols-2 gap-5">

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.payment_failure_process.view.payment.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.payment_failure_process.view.payment.amount') }}</dt>
              <dd><Currency :currency="payment_failure_process.payment_attempt.invoice.currency" :amount="payment_failure_process.payment_attempt.invoice.amount" /></dd>
            </div>
            <div>
              <dt>{{ $t('app.workflows.payment_failure_process.view.payment.customer') }}</dt>
              <dd><router-link :to="{name: 'app.customer.view', params: {id: payment_failure_process.customer.id}}">{{ payment_failure_process.customer.email }}</router-link></dd>
            </div>
          </dl>
          <router-link :to="{name: 'app.invoices.view', params: {id: payment_failure_process.payment_attempt.invoice.id}}" class="btn--container">{{ $t('app.workflows.payment_failure_process.view.payment.view') }}</router-link>
        </div>

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.payment_failure_process.view.details.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.payment_failure_process.view.details.state') }}</dt>
              <dd>{{ payment_failure_process.state }}</dd>
            </div>
          </dl>
        </div>
      </div>
      <div class="card-body m-5" v-if="payment_failure_process.has_error">
        <div class="section-header">{{ $t('app.workflows.payment_failure_process.view.error.title') }}</div>
        <pre>{{ payment_failure_process.error }}</pre>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import Currency from "../../../../components/app/Currency.vue";

export default {
  name: "SubscriptionCreationView",
  components: {Currency},
  data() {
    return {
      ready: false,
      payment_failure_process: null,
      sending: false,
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/system/payment-failure-process/'+id+'/view').then(response => {
      this.payment_failure_process = response.data.payment_failure_process;
      this.ready = true;
    })
  },
  methods: {
    callProcess: function () {
      const id = this.$route.params.id;
      this.sending = true;
      axios.post('/app/system/payment-failure-process/'+id+'/process').then(response => {
        this.payment_failure_process = response.data;
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