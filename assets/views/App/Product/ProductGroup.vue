<template>
  <div class="flex">
    <div class="w-1/5">
      <div class="submenu-container">
        <div class="submenu-title">{{ $t('app.product.menu.title') }}</div>
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.product.list'}" class="submenu-link"><i class="fa-solid fa-box"></i> {{ $t('app.product.menu.products') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.feature.list'}" class="submenu-link"><i class="fa-sharp fa-solid fa-gear"></i> {{ $t('app.product.menu.features') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.vouchers.list'}" class="submenu-link"><i class="fa-solid fa-gift"></i> {{ $t('app.product.menu.vouchers') }}</router-link></li>
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
  name: "ProductGroup"
}
</script>

<style scoped>

</style>