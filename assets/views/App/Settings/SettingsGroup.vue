<template>
  <div class="flex">
    <div class="w-1/5">
      <div class="submenu-container">
        <div class="submenu-title">{{ $t('app.settings.menu.title') }}</div>
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.user.settings'}" class="submenu-link">{{ $t('app.settings.menu.user_settings') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.pdf_template.list'}" class="submenu-link">{{ $t('app.settings.menu.pdf_templates') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.email_template.list'}" class="submenu-link">{{ $t('app.settings.menu.email_templates') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.brand_settings.list'}" class="submenu-link">{{ $t('app.settings.menu.brand_settings') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.notification_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.notification_settings') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.system_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.system_settings') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.users.list'}" class="submenu-link">{{ $t('app.settings.menu.users') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.import.stripe'}" class="submenu-link">{{ $t('app.settings.menu.stripe') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.api_keys.main'}" class="submenu-link">{{ $t('app.settings.menu.api_keys') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.settings.exchange_rates.list'}" class="submenu-link">{{ $t('app.settings.menu.exchange_rates') }}</router-link></li>




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