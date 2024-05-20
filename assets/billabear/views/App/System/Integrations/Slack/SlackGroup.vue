<template>
  <div class="grid grid-cols-6">
    <div class="border-r">
      <span class="p-5 text-2xl font-bold">{{ $t('app.system.integrations.slack.menu.title') }}</span>
      <ul>
        <li><router-link :to="{name: 'app.system.integrations.slack.webhook'}">{{ $t('app.system.integrations.slack.menu.webhooks') }}</router-link></li>
        <li><router-link :to="{name: 'app.system.integrations.slack.notification'}">{{ $t('app.system.integrations.slack.menu.notification') }}</router-link></li>


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

</style>
