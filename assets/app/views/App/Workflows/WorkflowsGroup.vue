<template>
  <div class="">

    <div class="">
      <div class="submenu-container">
        <ul>
          <li class="submenu-list-item"><router-link :to="{name: 'app.workflows.cancellation_request.list'}" class="submenu-link">{{ $t('app.workflows.menu.cancellation_requests') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.workflows.subscription_creation.list'}" class="submenu-link">{{ $t('app.workflows.menu.subscription_creation') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.workflows.payment_creation.list'}" class="submenu-link">{{ $t('app.workflows.menu.payment_creation') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.workflows.charge_back_creation.list'}" class="submenu-link">{{ $t('app.workflows.menu.charge_back_creation') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.workflows.refund_created_process.list'}" class="submenu-link">{{ $t('app.workflows.menu.refund_created_process') }}</router-link></li>
          <li class="submenu-list-item"><router-link :to="{name: 'app.workflows.payment_failure_process.list'}" class="submenu-link">{{ $t('app.workflows.menu.payment_failure_process') }}</router-link></li>
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
  name: "SystemGroup"
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