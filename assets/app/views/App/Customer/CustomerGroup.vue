<template>

  <div class="">
    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.customer.list'}" class="submenu-link">{{ $t('app.customer.menu.customers') }}</router-link></li>
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
  name: "CustomerGroup"
}
</script>

<style scoped>

.router-link-active {
  all: unset;
  @apply  p-3 dark:text-gray-100 cursor-pointer;
}
.router-link-exact-active {
  @apply bg-gray-100 text-black p-3 rounded-lg dark:text-gray-200 dark:bg-gray-700;
}
</style>