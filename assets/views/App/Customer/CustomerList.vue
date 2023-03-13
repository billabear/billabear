<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.list.title') }}</h1>

    <div class="top-button-container">
      <router-link :to="{name: 'app.customer.create'}" class="btn--main"><i class="fa-solid fa-user-plus"></i> {{ $t('app.customer.list.create_new') }}</router-link>
    </div>
    <div class="mt-3 card-body">
      <LoadingScreen :ready="ready">
        <table class="table-auto w-full">
          <thead>
            <tr>
              <th>{{ $t('app.customer.list.email') }}</th>
              <th>{{ $t('app.customer.list.country')}}</th>
              <th>{{ $t('app.customer.list.reference') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="customer in customers">
              <td>{{ customer.email }}</td>
              <td>{{ customer.country }}</td>
              <td>{{ customer.reference }}</td>
            </tr>
            <tr v-if="customers.length === 0">
              <td colspan="4" class="text-center">{{ $t('app.customer.list.no_customers') }}</td>
            </tr>
          </tbody>
        </table>
      </LoadingScreen>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerList.vue",
  data() {
    return {
      ready: false,
      customers: [],
      has_more: false,
      last_key: null,
    }
  },
  mounted() {
    axios.get('/app/customer').then(response => {
      this.customers = response.data.data;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>