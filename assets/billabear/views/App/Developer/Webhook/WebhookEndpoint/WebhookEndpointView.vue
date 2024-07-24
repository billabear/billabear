<template>
  <div>
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.system.webhooks.webhook_endpoint.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error" class="p-5">
        <div class="card-body">

          <h2 class="section-header">{{ $t('app.system.webhooks.webhook_endpoint.view.main.title') }}</h2>
          <div class="section-body">

            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.system.webhooks.webhook_endpoint.view.main.name') }}</dt>
                <dd>{{ endpoint.name }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.system.webhooks.webhook_endpoint.view.main.url') }}</dt>
                <dd>{{ endpoint.url }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "WebhookEndpointView",
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      endpoint: {}
    }
  },
  mounted() {

    var endpointId = this.$route.params.id
    this.id = endpointId;
    axios.get('/app/developer/webhook/'+endpointId+'/view').then(response => {
      this.endpoint = response.data.webhook_endpoint;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
        this.errorMessage = this.$t('app.product.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.product.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>