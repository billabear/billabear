<template>
  <div class="flex">
    <div class="w-1/5">
      <div class="mt-5 rounded-lg p-5 border shadow-lg">
        <ul>
          <li class="my-3"><router-link :to="{name: 'app.user.settings'}">{{ $t('app.settings.menu.user_settings') }}</router-link></li>
          <li class="my-3"><router-link :to="{name: 'app.settings.pdf_template.list'}">{{ $t('app.settings.menu.pdf_templates') }}</router-link></li>
          <li class="my-3"><router-link :to="{name: 'app.settings.email_template.list'}">{{ $t('app.settings.menu.email_templates') }}</router-link></li>
          <li class="my-3"><router-link :to="{name: 'app.settings.brand_settings.list'}">{{ $t('app.settings.menu.brand_settings') }}</router-link></li>
          <li class="my-3"><router-link :to="{name: 'app.user.invite'}">{{ $t('app.settings.menu.invite') }}</router-link></li>

        </ul>
      </div>
    </div>

    <div class="w-4/5 m-5">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  name: "SettingsGroup"
}
</script>

<style scoped>

</style>