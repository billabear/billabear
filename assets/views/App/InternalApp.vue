<template>
  <div class="lg:flex  main-background">
    <div class="dashboard-sidebar menu-background">
      <AppLogo />
      <Menu>
        <MenuGroup :name="$t('app.menu.main.title')">
          <MenuItem route-name="app.home"><i class="fa-solid fa-house"></i> {{ $t('app.menu.main.home') }}</MenuItem>
          <MenuItem route-name="app.team"><i class="fa-solid fa-people-group"></i> {{ $t('app.menu.main.team') }}</MenuItem>
          <MenuItem route-name="app.plan"><i class="fa-regular fa-pen-to-square"></i> {{ $t('app.menu.main.plan') }}</MenuItem>
          <MenuItem route-name="app.user.settings"><i class="fa-solid fa-gear"></i> {{ $t('app.menu.main.user_settings') }}</MenuItem>
          <MenuItem route-name="app.billing.details"><i class="fa-solid fa-coins"></i> {{ $t('app.menu.main.billing') }}</MenuItem>
          <MenuItem route-name="app.user.invite"><i class="fa-solid fa-user-plus"></i> {{ $t('app.menu.main.user_invite') }}</MenuItem>
        </MenuGroup>
      </Menu>
    </div>
    <div class="w-full h-screen lg:p-4 lg:m-8 p-2">
        <router-view></router-view>
    </div>
  </div>

</template>

<script>
import AppLogo from "../../components/app/AppLogo";
export default {
  name: "InternalApp",
  components: {AppLogo}
}
</script>

<style scoped>

</style>
