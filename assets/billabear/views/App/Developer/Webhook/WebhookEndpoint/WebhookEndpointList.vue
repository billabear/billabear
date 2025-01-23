<template>
  <div>
    <div class="grid grid-cols-2">
    <h1 class="page-title">{{ $t('app.system.webhooks.webhook_endpoint.list.title') }}</h1>

      <div class="mt-3 text-end">
        <router-link :to="{name: 'app.system.webhook_endpoints.create'}" class="btn--main"><i class="fa-solid fa-plus"></i>{{ $t('app.system.webhooks.webhook_endpoint.list.add') }}</router-link>
      </div>
    </div>
    <LoadingScreen :ready="ready">

      <div class="rounded-lg bg-white shadow p-3">
        <table class="w-full">
          <thead>
          <tr class="border-b border-black">
            <th class="text-left pb-2">{{ $t('app.system.webhooks.webhook_endpoint.list.list.name') }}</th>
            <th class="text-left pb-2">{{ $t('app.system.webhooks.webhook_endpoint.list.list.url')}}</th>
            <th class="text-left pb-2">{{ $t('app.system.webhooks.webhook_endpoint.list.list.status') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
            <tr v-if="list.length == 0">
              <td colspan="4" class="py-3 text-center">{{ $t('app.system.webhooks.webhook_endpoint.list.no_endpoints') }}</td>
            </tr>
            <tr v-for="item in list">
              <td class="py-3">{{ item.name }}</td>
              <td class="py-3">{{ item.url }}</td>
              <td class="py-3">{{ item.status }}</td>
              <td class="py-3"><router-link :to="{name: 'app.system.webhook_endpoints.view', params: {id: item.id}}" class="btn--main">{{ $t('app.system.webhooks.webhook_endpoint.list.view') }}</router-link></td>
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
