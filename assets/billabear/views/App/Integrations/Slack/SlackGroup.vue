<template>
  <div class="grid grid-cols-6 gap-3">
    <div class="border-r">
      <span class="block my-3 text-2xl font-bold dark:text-white">{{ $t('app.system.integrations.slack.menu.title') }}</span>
      <ul >
        <li class="py-1"><router-link :to="{name: 'app.system.integrations.slack.webhook'}" class="slack-menu">{{ $t('app.system.integrations.slack.menu.webhooks') }}</router-link></li>
        <li class="py-1"><router-link :to="{name: 'app.system.integrations.slack.notification'}" class="slack-menu">{{ $t('app.system.integrations.slack.menu.notification') }}</router-link></li>
      </ul>
    </div>
    <div class="col-span-5">
      <router-view></router-view>
    </div>
  </div>
</template>

<script setup>
// Component migrated from Options API to Composition API
// This component requires no reactive data or methods, so script setup is minimal
</script>

<style scoped>
.slack-menu {
  @apply px-2 text-black underline dark:text-white;
}
</style>
