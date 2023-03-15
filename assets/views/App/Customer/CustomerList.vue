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
            <tr v-for="customer in customers" class="mt-5">
              <td>{{ customer.email }}</td>
              <td>{{ customer.country }}</td>
              <td>{{ customer.reference }}</td>
              <td class="mt-2"><router-link :to="{name: 'app.customer.view', params: {id: customer.id}}" class="btn--main">View</router-link></td>
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
      previous_last_key: null,
      next_page_in_progress: false
    }
  },
  mounted() {
    axios.get('/app/customer').then(response => {
      this.customers = response.data.data;
      this.has_more = response.data.has_more;
      this.last_key = response.data.last_key;
      this.ready = true;
    })
  },
  methods: {
    nextPage: function () {
      // To go backwards
      this.previous_last_key = this.last_key;
      this.next_page_in_progress = true;
      axios.get('/app/customer?last_key='+this.last_key).then(response => {
        this.customers = response.data.data;
        this.has_more = response.data.has_more;
        this.last_key = response.data.last_key;
        this.next_page_in_progress = false;
      })
    },
    prevPage: function () {

    }
  }
}
</script>

<style scoped>

</style>