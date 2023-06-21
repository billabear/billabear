<template>
  <div class="flex">
    <div class="w-1/5">
      <div class="submenu-container">
        <div class="submenu-title">{{ $t('app.subscription.menu.title') }}</div>
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.subscription.list'}" class="submenu-link"><i class="fa-solid fa-arrows-rotate"></i> {{ $t('app.subscription.menu.subscriptions') }}</router-link></li>

        </ul>
      </div>
    </div>

    <div class="w-4/5 m-5">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "SubscriptionGroup",
  components: {RoleOnlyView}
}
</script>

<style scoped>

</style>