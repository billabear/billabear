<template>
  <div class="grid grid-cols-6">
    <div class="border-r">
      <span class="my-3 p-5 text-2xl font-bold">{{ $t('app.system.integrations.slack.menu.title') }}</span>
      <ul class="mt-5">
        <li><router-link :to="{name: 'app.system.integrations.slack.webhook'}" class="slack-menu">{{ $t('app.system.integrations.slack.menu.webhooks') }}</router-link></li>
        <li><router-link :to="{name: 'app.system.integrations.slack.notification'}" class="slack-menu">{{ $t('app.system.integrations.slack.menu.notification') }}</router-link></li>


      </ul>
    </div>
    <div class="col-span-5">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: "SlackGroup"
}
</script>

<style scoped>
.slack-menu {
  @apply px-2 text-black underline;
}
</style>
