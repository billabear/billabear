<template>

  <div class="">

    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.subscriptions'}" class="submenu-link">{{ $t('app.reports.menu.subscriptions') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.churn'}" class="submenu-link">{{ $t('app.reports.menu.churn') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.lifetime'}" class="submenu-link">{{ $t('app.reports.menu.lifetime') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.report.tax'}" class="submenu-link">{{ $t('app.reports.menu.tax') }}</router-link></li>

        </ul>
      </div>
    </div>

    <div class="p-5" style="max-width: 85%">
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
.calculated-width {
  width: -webkit-calc(100% - 100px);
  width: -moz-calc(100% - 100px);
  width: calc(100% - 100px);
}
.router-link-active {
  all: unset;
  @apply  p-3;
}
.router-link-exact-active {
  @apply bg-gray-100 text-black p-3 rounded-lg dark:text-gray-200 dark:bg-gray-700;
}
</style>
