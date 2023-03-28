<template>
  <div class="lg:flex  main-background">
    <div class="dashboard-sidebar menu-background">
      <AppLogo />
      <Menu>
        <MenuGroup :name="$t('app.menu.main.title')">
          <MenuItem route-name="app.home"><i class="fa-solid fa-house"></i> {{ $t('app.menu.main.home') }}</MenuItem>
          <MenuItem route-name="app.customer.list"><i class="fa-solid fa-coins"></i> {{ $t('app.menu.main.customer') }}</MenuItem>
          <MenuItem route-name="app.product.list"><i class="fa-solid fa-coins"></i> {{ $t('app.menu.main.product') }}</MenuItem>
          <MenuItem route-name="app.user.settings"><i class="fa-solid fa-gear"></i> {{ $t('app.menu.main.user_settings') }}</MenuItem>
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
