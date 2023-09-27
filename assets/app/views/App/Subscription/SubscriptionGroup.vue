<template>
  <div class="">
    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.subscription.list'}" class="submenu-link">{{ $t('app.subscription.menu.subscriptions') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.subscription.mass_change.list'}" class="submenu-link">{{ $t('app.subscription.menu.mass_change') }}</router-link></li>
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
  name: "SubscriptionGroup",
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