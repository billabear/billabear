<template>
  <div class="">

    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.system.webhooks'}" class="submenu-link">{{ $t('app.system.menu.webhooks') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.system.country.list'}" class="submenu-link">{{ $t('app.system.menu.countries') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.system.tax_type.list'}" class="submenu-link">{{ $t('app.system.menu.tax_types') }}</router-link></li>
        </ul>
      </div>
    </div>

    <div class="">
      <router-view></router-view>
    </div>
  </div>

</template>

<script>
export default {
  name: "SystemGroup"
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
