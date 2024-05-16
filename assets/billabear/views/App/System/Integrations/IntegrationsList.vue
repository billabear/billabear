<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.system.integrations.list.title') }}</h1>
    <div class="mt-3">
      <table class="list-table">
        <thead>
        <tr>
          <th>{{ $t('app.system.integrations.list.list.name') }}</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ $t('app.system.integrations.list.stripe.name') }}</td>
            <th><router-link class="btn--main" :to="{name: 'app.system.integrations.stripe'}">{{ $t('app.system.integrations.list.stripe.button') }}</router-link></th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "IntegrationsList"
}
</script>

<style scoped>

</style>
