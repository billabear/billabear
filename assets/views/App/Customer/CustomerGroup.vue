<template>

  <div class="flex">
    <div class="w-1/5">
      <div class="mt-5 rounded-lg p-5 border shadow-lg">
        <ul>
          <li class="my-3"><router-link :to="{name: 'app.customer.list'}">{{ $t('app.customer.menu.customers') }}</router-link></li>
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
  name: "CustomerGroup"
}
</script>

<style scoped>

</style>