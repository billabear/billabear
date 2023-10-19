<template>
  <div>
    <h1 class="page-title">{{ $t('app.workflows.charge_back_creation.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mx-5 grid grid-cols-2 gap-5">

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.charge_back_creation.view.payment.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.charge_back_creation.view.payment.customer') }}</dt>
              <dd><router-link :to="{name: 'app.customer.view', params: {id: charge_back_creation.payment.customer.id}}">{{
                  charge_back_creation.payment.customer.email
                }}</router-link></dd>
            </div>
          </dl>
          <router-link :to="{name: 'app.payment.view', params: {id: charge_back_creation.charge_back.payment.id}}" class="btn--container">{{ $t('app.workflows.charge_back_creation.view.payment.view') }}</router-link>
        </div>

        <div class="card-body">
          <div class="section-header">{{ $t('app.workflows.charge_back_creation.view.details.title') }}</div>
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.workflows.charge_back_creation.view.details.state') }}</dt>
              <dd>{{ charge_back_creation.state }}</dd>
            </div>
          </dl>
        </div>
      </div>
      <div class="card-body m-5" v-if="charge_back_creation.has_error">
        <div class="section-header">{{ $t('app.workflows.charge_back_creation.view.error.title') }}</div>
        <pre>{{ charge_back_creation.error }}</pre>
      </div>
      <div class="m-5">
        <SubmitButton :in-progress="sending" class="btn--secondary" @click="callProcess" v-if="charge_back_creation.state !== 'completed'">{{ $t('app.workflows.charge_back_creation.view.buttons.process') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ChargeBackCreationView",
  data() {
    return {
      ready: false,
      charge_back_creation: null,
      sending: false,
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/system/charge-back-creation/'+id+'/view').then(response => {
      this.charge_back_creation = response.data.payment_creation;
      this.ready = true;
    })
  },
  methods: {
    callProcess: function () {
      const id = this.$route.params.id;
      this.sending = true;
      axios.post('/app/system/charge-back-creation/'+id+'/process').then(response => {
        this.charge_back_creation = response.data;
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