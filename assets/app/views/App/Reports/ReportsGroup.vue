<template>

  <div class="flex">

    <div class="w-1/5">
      <div class="submenu-container">
        <div class="text-xl font-bold p-5">{{ $t('app.reports.menu.title') }}</div>
        <ul class="mb-5">
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.dashboard'}" class="submenu-link"><i class="fa-solid fa-gauge"></i> {{ $t('app.reports.menu.dashboard') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.expiring_cards.list'}" class="submenu-link"><i class="fa-solid fa-credit-card"></i> {{ $t('app.reports.menu.expiring_cards') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.subscriptions'}" class="submenu-link"><i class="fa-solid fa-arrows-rotate"></i>  {{ $t('app.reports.menu.subscriptions') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.vat'}" class="submenu-link"><i class="fa-solid fa-coins"></i> {{ $t('app.reports.menu.vat') }}</router-link></li>


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
  name: "ReportsGroup"
}
</script>

<style scoped>

</style>