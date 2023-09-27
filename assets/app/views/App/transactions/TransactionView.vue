<template>

  <div class="">

    <div>
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.payment.list'}" class="submenu-link">{{ $t('app.transactions.menu.payments') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.refund.list'}" class="submenu-link">{{ $t('app.transactions.menu.refunds') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.charge_backs.list'}" class="submenu-link">{{ $t('app.transactions.menu.charge_backs') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.checkout.list'}" class="submenu-link">{{ $t('app.transactions.menu.checkout') }}</router-link></li>
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
  name: "TransactionView"
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