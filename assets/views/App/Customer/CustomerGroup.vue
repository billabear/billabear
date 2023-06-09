<template>

  <div class="flex">
    <div class="w-1/5">
      <div class="submenu-container">
        <div class="submenu-title">{{ $t('app.customer.menu.title') }}</div>
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.customer.list'}" class="submenu-link"><i class="fa-solid fa-user"></i> {{ $t('app.customer.menu.customers') }}</router-link></li>
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
  name: "CustomerGroup"
}
</script>

<style scoped>

</style>