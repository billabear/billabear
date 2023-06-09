<template>

  <div class="flex">

    <div class="w-1/5">
      <div class="submenu-container">
        <div class="submenu-title">{{ $t('app.transactions.menu.title') }}</div>
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.payment.list'}" class="submenu-link">{{ $t('app.transactions.menu.payments') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.refund.list'}" class="submenu-link">{{ $t('app.transactions.menu.refunds') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.charge_backs.list'}" class="submenu-link">{{ $t('app.transactions.menu.charge_backs') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.list'}" class="submenu-link">{{ $t('app.transactions.menu.invoices') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.invoices.unpaid_list'}" class="submenu-link">{{ $t('app.transactions.menu.unpaid_invoices') }}</router-link></li>
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
  name: "TransactionView"
}
</script>

<style scoped>

</style>