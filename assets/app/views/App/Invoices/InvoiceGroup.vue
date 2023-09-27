<template>
  <div class="">
    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.list'}" class="submenu-link">{{ $t('app.invoices.menu.invoices') }}</router-link></li>
          <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
            <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.create'}" class="submenu-link">{{ $t('app.invoices.menu.create') }}</router-link></li>
          </RoleOnlyView>

          <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.unpaid_list'}" class="submenu-link">{{ $t('app.invoices.menu.unpaid_invoices') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.quotes.list'}" class="submenu-link">{{ $t('app.invoices.menu.quotes') }}</router-link></li>
        </ul>
      </div>
    </div>

    <div class="">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "InvoiceGroup",
  components: {RoleOnlyView}
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