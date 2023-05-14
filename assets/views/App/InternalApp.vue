<template>
  <div class="">
    <div class="page-header">
      <AppLogo />
      <Menu>
        <MenuGroup>
          <MenuItem route-name="app.report.dashboard">{{ $t('app.menu.main.reports') }}</MenuItem>
          <MenuItem route-name="app.transactions">{{ $t('app.menu.main.transactions') }}</MenuItem>
          <MenuItem route-name="app.product">{{ $t('app.menu.main.products') }}</MenuItem>
          <MenuItem route-name="app.customer.list">{{ $t('app.menu.main.customers') }}</MenuItem>
          <MenuItem route-name="app.subscription.list">{{ $t('app.menu.main.subscriptions') }}</MenuItem>
          <MenuItem route-name="app.user.settings">{{ $t('app.menu.main.settings') }}</MenuItem>
        </MenuGroup>
      </Menu>
    </div>
    <div class="">
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
