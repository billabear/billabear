<template>
  <div class="flex">

    <div class="w-1/5">
      <div class="submenu-container">
        <div class="text-xl font-bold p-5">{{ $t('app.system.menu.title') }}</div>
        <ul class="mb-5">
          <li class="submenu-list-item"><router-link :to="{name: 'app.system.webhooks'}" class="submenu-link"><i class="fa-solid fa-gauge"></i> {{ $t('app.system.menu.webhooks') }}</router-link></li>

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
  name: "SystemGroup"
}
</script>

<style scoped>

</style>