<template>
  <div>
    <div class="grid grid-cols-2">
      <div>

        <h1 class="page-title">{{ $t('app.system.webhooks.main.title') }}</h1>
      </div>
      <div class="text-end">
        <router-link :to="{name: 'app.system.webhook_endpoints.list'}" class="btn--main"><i class="fa-solid fa-gear"></i> {{ $t('app.system.webhooks.main.manage_endpoints') }}</router-link>
      </div>
    </div>

    <div>test</div>
  </div>
</template>

<script>
export default {
  name: "WebhookMain"
}
</script>

<style scoped>

</style>