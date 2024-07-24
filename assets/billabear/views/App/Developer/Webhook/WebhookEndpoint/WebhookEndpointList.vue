<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.system.webhooks.webhook_endpoint.list.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mr-3 text-end">
        <router-link :to="{name: 'app.system.webhook_endpoints.create'}" class="btn--main"><i class="fa-solid fa-plus"></i>{{ $t('app.system.webhooks.webhook_endpoint.list.add') }}</router-link>
      </div>
      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.system.webhooks.webhook_endpoint.list.list.name') }}</th>
            <th>{{ $t('app.system.webhooks.webhook_endpoint.list.list.url')}}</th>
            <th>{{ $t('app.system.webhooks.webhook_endpoint.list.list.status') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
            <tr v-if="list.length == 0">
              <td colspan="4" class="text-center">{{ $t('app.system.webhooks.webhook_endpoint.list.no_endpoints') }}</td>
            </tr>
            <tr v-for="item in list">
              <td>{{ item.name }}</td>
              <td>{{ item.url }}</td>
              <td>{{ item.status }}</td>
              <td><router-link :to="{name: 'app.system.webhook_endpoints.view', params: {id: item.id}}" class="btn--main">{{ $t('app.system.webhooks.webhook_endpoint.list.view') }}</router-link></td>
            </tr>
          </tbody>
        </table>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "WebhookEndpointList",
  data() {
      return {
        list: [],
        ready: false,
      }
  },
  mounted() {
    axios.get("/app/developer/webhook").then(response => {
      this.list = response.data.data
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>