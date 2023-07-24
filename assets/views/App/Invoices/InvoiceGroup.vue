<template>
  <div class="flex">
    <div class="w-1/5">
      <div class="submenu-container">
        <div class="submenu-title">{{ $t('app.invoices.menu.title') }}</div>
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.list'}" class="submenu-link"><i class="fa-solid fa-file-invoice"></i> {{ $t('app.invoices.menu.invoices') }}</router-link></li>
          <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
            <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.create'}" class="submenu-link"><i class="fa-solid fa-plus"></i> {{ $t('app.invoices.menu.create') }}</router-link></li>
          </RoleOnlyView>

          <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.unpaid_list'}" class="submenu-link"><i class="fa-solid fa-file-invoice-dollar"></i> {{ $t('app.invoices.menu.unpaid_invoices') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.quotes.list'}" class="submenu-link"><i class="fa-solid fa-file-invoice-dollar"></i> {{ $t('app.invoices.menu.quotes') }}</router-link></li>
        </ul>
      </div>
    </div>

    <div class="w-4/5 m-5">
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

</style>