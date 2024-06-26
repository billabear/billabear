<template>
  <div v-if="!has_error">
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.subscription.list.title') }}</h1>

    <div class="top-button-container">
      <Dropdown text="Filters" v-if="Object.keys(filters).length > 0">
        <div class="list_container">
          <ListGroup>
            <ListGroupItem v-for="(filter, filterKey) in filters">
              <input type="checkbox" @change="toogle(filterKey)" :checked="isActive(filterKey)" class="filter_field" :id="'filter_'+filterKey" /> <label :for="'filter_'+filterKey">{{ $t(''+filter.label+'') }}</label>
            </ListGroupItem>
          </ListGroup>
        </div>
      </Dropdown>
    </div>

    <div class="card-body my-5" v-if="active_filters.length > 0">
      <h2>{{ $t('app.subscription.list.filter.title') }}</h2>
        <form @submit.prevent="doSearch">
        <div v-for="filter in active_filters">
          <div class="px-3 py-1 sm:flex sm:px-6">
            <div class="w-1/6">{{ $t(''+this.filters[filter].label+'') }}</div>
            <div><input v-if="this.filters[filter].type == 'text'" type="text" class="filter_field" v-model="this.filters[filter].value" /></div>
          </div>
        </div>

        <button @click="doSearch" class="flex items-center justify-center w-1/2 px-5 py-2 text-sm tracking-wide text-white transition-colors duration-200 bg-blue-500 rounded-lg shrink-0 sm:w-auto gap-x-2 hover:bg-blue-600 dark:hover:bg-blue-500 dark:bg-blue-600">{{ $t('app.customer.list.filter.search') }}</button>
      </form>
    </div>

    <LoadingScreen :ready="ready">
      <div class="mt-3">
          <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.subscription.list.email') }}</th>
            <th>{{ $t('app.subscription.list.plan')}}</th>
            <th>{{ $t('app.subscription.list.status') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody v-if="loaded">
          <tr v-for="subscription in subscriptions" class="mt-5">
            <td>{{ subscription.customer.email }}</td>
            <td v-if="subscription.plan !== null && subscription.plan !== undefined">{{ subscription.plan.name }}</td>
            <td v-else></td>
            <td>{{ subscription.status }}</td>
            <td><router-link :to="{name: 'app.subscription.view', params: {subscriptionId: subscription.id}}" class="btn--main">{{ $t('app.subscription.list.view') }}</router-link></td>
          </tr>
          <tr v-if="subscriptions.length === 0">
            <td colspan="4" class="text-center">{{ $t('app.subscription.list.no_subscriptions') }}</td>
          </tr>
          </tbody>
          <tbody v-else>
          <tr>
            <td colspan="4" class="text-center">
              <LoadingMessage>{{ $t('app.subscription.list.loading') }}</LoadingMessage>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="sm:grid sm:grid-cols-2">

        <div class="mt-4">
          <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.subscription.list.prev') }}</button>
          <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.subscription.list.next') }}</button>
        </div>
        <div class="mt-4 text-end">
          <select @change="changePerPage" v-model="per_page">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>
      </div>
    </LoadingScreen>
  </div>
  <div v-else class="error-page">
    {{ $t('app.subscription.list.error_message') }}
  </div>
</template>

<script>
import axios from "axios";
import InternalApp from "../InternalApp.vue";

export default {
  name: "SubscriptionList.vue",
  components: {InternalApp},
  data() {
    return {
      ready: false,
      subscriptions: [],
      has_more: false,
      has_error: false,
      loaded: true,
      last_key: null,
      first_key: null,
      previous_last_key: null,
      next_page_in_progress: false,
      show_back: false,
      show_filter_menu: false,
      active_filters: [],
      per_page: "10",
      filters: {}
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
    syncQueryToFilters: function () {
      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          this.filters[key].value = this.$route.query[key];
          if (!this.isActive(key)) {
            this.active_filters.push(key);
          }
        } else {
          this.filters[key].value = null;
          if (this.active_filters.indexOf(key) !== -1) {
            console.log(key)
            this.active_filters.splice( this.active_filters.indexOf(key) , 1) ;
          }
        }
      });
    },
    doSearch: function () {
      var queryVals = this.buildFilterQuery();
      this.$router.push({query: queryVals})
    },
    buildFilterQuery: function () {
      var queryVals = {};
      for (var i = 0; i < this.active_filters.length; i++) {
        var filter = this.active_filters[i];
        if (this.filters[filter].value !== null && this.filters[filter].value !== undefined) {

          queryVals[filter] = this.filters[filter].value;
        }
      }

      if (this.$route.query.per_page !== undefined) {
        queryVals.per_page = this.$route.query.per_page;
        this.per_page=this.$route.query.per_page;
      }

      return queryVals;
    },
    nextPage: function () {

      var queryVals = this.buildFilterQuery();
      queryVals.last_key = this.last_key;
      this.$router.push({query: queryVals})
    },
    prevPage: function () {
      var queryVals = this.buildFilterQuery();
      queryVals.first_key = this.first_key;
      this.$router.push({query: queryVals})
    },
    changePerPage: function ($event) {
      var queryVals = this.buildFilterQuery();
      queryVals.per_page = $event.target.value;
      this.per_page=queryVals.per_page;

      if (this.$route.query.last_key !== undefined) {
        queryVals.last_key = this.$route.query.last_key;
      } else if (this.$route.query.first_key !== undefined) {
        queryVals.first_key = this.$route.query.first_key;
      }

      this.$router.push({query: queryVals});
    },
    doStuff: function ()
    {
      this.syncQueryToFilters();
      var mode = 'normal';
      let urlString = '/app/subscription?';

      if (this.$route.query.last_key !== undefined) {
        urlString = urlString + '&last_key=' +  encodeURIComponent(this.$route.query.last_key);
        this.show_back = true;
        mode = 'normal';
      } else if (this.$route.query.first_key !== undefined) {
        urlString = urlString + '&first_key=' +  encodeURIComponent(this.$route.query.first_key);
        this.has_more = true;
        mode = 'first_key';
      }

      if (this.$route.query.per_page !== undefined) {
        urlString = urlString + '&per_page=' + this.$route.query.per_page;
      }

      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          urlString = urlString + '&'+key+'=' + encodeURIComponent(this.$route.query[key]);
        }
      });
      this.loaded = false;
      axios.get(urlString).then(response => {

        this.subscriptions = response.data.data;
        if (mode === 'normal') {
          this.has_more = response.data.has_more;
        } else {
          this.show_back = response.data.has_more;
          this.has_more = true;
        }
        this.last_key = response.data.last_key;
        this.first_key = response.data.first_key;
        this.ready = true;
        this.loaded = true;
      }).catch(error => {
        this.has_error = true;
      })

    },
    toogle: function (key) {
      var newFilters = [];
      var found = false;
      for (var i = 0; i < this.active_filters.length; i++) {
        if (this.active_filters[i] !== key) {

          newFilters.push(this.active_filters[i]);
        } else {
          found = true;
        }
      }
      if (!found) {
        newFilters.push(key);
      }
      this.active_filters = newFilters;
    },
    isActive: function (key) {
      for (var i = 0; i < this.active_filters.length; i++) {
        if (this.active_filters[i] === key) {
          return true;
        }
      }
      return false;
    }
  }
}
</script>

<style scoped>

</style>
