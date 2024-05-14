<template>
  <div class="">
    <div class="">
      <div class="submenu-container">
        <ul>
          <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.pdf_template.list'}" class="submenu-link">{{ $t('app.settings.menu.pdf_templates') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.email_template.list'}" class="submenu-link">{{ $t('app.settings.menu.email_templates') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.brand_settings.list'}" class="submenu-link">{{ $t('app.settings.menu.brand_settings') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.exchange_rates.list'}" class="submenu-link">{{ $t('app.settings.menu.exchange_rates') }}</router-link></li>
          </RoleOnlyView>
         <RoleOnlyView role="ROLE_ADMIN">
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.tax_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.tax_settings') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.import.stripe'}" class="submenu-link">{{ $t('app.settings.menu.stripe') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.users.list'}" class="submenu-link">{{ $t('app.settings.menu.users') }}</router-link></li>
          </RoleOnlyView>
          <RoleOnlyView role="ROLE_DEVELOPER">
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.notification_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.notification_settings') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.system_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.system_settings') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.settings.api_keys.main'}" class="submenu-link">{{ $t('app.settings.menu.api_keys') }}</router-link></li>
          </RoleOnlyView>
        </ul>
      </div>
    </div>

    <div class="">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "SettingsGroup",
  components: {RoleOnlyView}
}
</script>

<style scoped>

.router-link-active {
  all: unset;
  @apply  p-3;
}
.router-link-exact-active {
  @apply bg-gray-100 text-black p-3 rounded-lg dark:text-gray-200 dark:bg-gray-700;
}
</style>