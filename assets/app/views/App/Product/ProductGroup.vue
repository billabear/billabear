<template>
  <div class="">
    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.product.list'}" class="submenu-link">{{ $t('app.product.menu.products') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.feature.list'}" class="submenu-link">{{ $t('app.product.menu.features') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.vouchers.list'}" class="submenu-link">{{ $t('app.product.menu.vouchers') }}</router-link></li>
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
  name: "ProductGroup"
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