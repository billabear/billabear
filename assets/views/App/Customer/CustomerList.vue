<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.list.title') }}</h1>

    <div class="top-button-container">
      <router-link :to="{name: 'app.customer.create'}" class="btn--main"><i class="fa-solid fa-user-plus"></i> {{ $t('app.customer.list.create_new') }}</router-link>
    </div>
    <LoadingScreen :ready="ready">
    <div class="mt-3 card-body">
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
    </div>
      <div class="mt-4">
        <router-link :to="{name: 'app.customer.list', query: {first_key: this.first_key}}" v-if="show_back" >{{ $t('app.customer.list.prev') }}</router-link>
        <router-link :to="{name: 'app.customer.list', query: {last_key: this.last_key}}" v-if="has_more" >{{ $t('app.customer.list.next') }}</router-link>
      </div>
    </LoadingScreen>
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
      first_key: null,
      previous_last_key: null,
      next_page_in_progress: false,
      show_back: false,
    }
  },
  mounted() {
    this.doStuff();
  },
  watch: {
    '$route.query': function (id) {
      this.doStuff()
    }
  },
  methods: {
    doStuff: function ()
    {
      var mode = 'normal';
      let urlString = '/app/customer?';
      if (this.$route.query.last_key !== undefined) {
        urlString = urlString + '&last_key=' + this.$route.query.last_key;
        this.show_back = true;
        mode = 'normal';
      } else if (this.$route.query.first_key !== undefined) {
        urlString = urlString + '&first_key=' + this.$route.query.first_key;
        this.has_more = true;
        mode = 'first_key';
      }

      console.log(urlString);
      axios.get(urlString).then(response => {

        this.customers = response.data.data;
        if (mode === 'normal') {
          this.has_more = response.data.has_more;
        } else {
          this.show_back = response.data.has_more;
          this.has_more = true;
        }
        this.last_key = response.data.last_key;
        this.first_key = response.data.first_key;
        this.ready = true;
      })

    },
  }
}
</script>

<style scoped>

</style>