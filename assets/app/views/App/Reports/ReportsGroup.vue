<template>

  <div class="">

    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.dashboard'}" class="submenu-link"><i class="fa-solid fa-gauge"></i> {{ $t('app.reports.menu.dashboard') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.expiring_cards.list'}" class="submenu-link"><i class="fa-solid fa-credit-card"></i> {{ $t('app.reports.menu.expiring_cards') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.subscriptions'}" class="submenu-link"><i class="fa-solid fa-arrows-rotate"></i>  {{ $t('app.reports.menu.subscriptions') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.vat'}" class="submenu-link"><i class="fa-solid fa-coins"></i> {{ $t('app.reports.menu.vat') }}</router-link></li>


        </ul>
      </div>
    </div>

    <div class="p-5">
      <router-view></router-view>
    </div>
  </div>


</template>

<script>
export default {
  name: "ReportsGroup"
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