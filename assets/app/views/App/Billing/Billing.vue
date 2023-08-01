<template>
    <div>
      <h1 class="page-title">{{ $t('app.billing.main.title') }}</h1>

      <div class="lg:flex body-panel mt-5">
        <div class="flex flex-col lg:w-80 border-r-1 p-3 border-r-1 border-slate-200">
          <ul>
            <li class="submenu-list-item"><router-link :to="{name: 'app.billing.details'}" class="submenu">{{ $t('app.billing.main.details') }}</router-link></li>
            <li class="submenu-list-item"><router-link :to="{name: 'app.billing.methods'}" class="submenu">{{ $t('app.billing.main.methods') }}</router-link></li>
            <li class="submenu-list-item">{{ $t('app.billing.main.invoices') }}</li>
          </ul>
        </div>

        <div class="w-full m-5">
          <router-view></router-view>
        </div>

      </div>
    </div>
</template>

<script>
export default {
  name: "Billing"
}
</script>

<style scoped>
.router-link-active  {
  @apply bg-white;
}

.router-link-exact-active {
  @apply bg-blue-100;
}
</style>
