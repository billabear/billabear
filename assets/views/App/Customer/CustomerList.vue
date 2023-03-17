<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.list.title') }}</h1>

    <div class="top-button-container">
      <router-link :to="{name: 'app.customer.create'}" class="btn--main"><i class="fa-solid fa-user-plus"></i> {{ $t('app.customer.list.create_new') }}</router-link>
      <div class="list">

        <div class="list_button">
          <button class="btn--secondary" @click="show_filter_menu = !show_filter_menu">
              <i v-if="!show_filter_menu" class="fa-solid fa-caret-down"></i>
              <i v-else class="fa-solid fa-caret-up"></i>
              {{ $t('app.customer.list.filter.button') }}
          </button>
        </div>
        <div class="list_container" v-if="show_filter_menu">
          <span v-for="(filter, filterKey) in filters" class="block">
            <input type="checkbox" @change="toogle(filterKey)" :checked="isActive(filterKey)" class="filter_field" /> {{ $t(''+filter.label+'') }}
          </span>
        </div>
      </div>
    </div>

    <div class="card-body my-5" v-if="active_filters.length > 0">
      <h2>{{ $t('app.customer.list.filter.title') }}</h2>
      <div v-for="filter in active_filters">
        <div class="px-3 py-1 sm:flex sm:px-6">
          <div class="w-1/6">{{ $t(''+this.filters[filter].label+'') }}</div>
          <div><input v-if="this.filters[filter].type == 'text'" type="text" class="filter_field" v-model="this.filters[filter].value" /></div>
        </div>
      </div>

      <button @click="doSearch" class="btn--main mt-3">{{ $t('app.customer.list.filter.search') }}</button>
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
        <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.customer.list.prev') }}</button>
        <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.customer.list.next') }}</button>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import InternalApp from "../InternalApp.vue";

export default {
  name: "CustomerList.vue",
  components: {InternalApp},
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
      show_filter_menu: false,
      active_filters: [],
      filters: {
        email: {
          label: 'app.customer.list.filter.email',
          type: 'text',
          value: null
        },
        reference: {
          label: 'app.customer.list.filter.reference',
          type: 'text',
          value: null
        },
        external_reference: {
          label: 'app.customer.list.filter.external_reference',
          type: 'text',
          value: null
        }
      }
    }
  },
  mounted() {
    this.addQueryToFilters();
    this.doStuff();

  },
  watch: {
    '$route.query': function (id) {
      this.doStuff()
    }
  },
  methods: {
    addQueryToFilters: function () {
      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          this.filters[key].value = this.$route.query[key];
          if (!this.isActive(key)) {
            this.active_filters.push(key);
          }
        } else {
          console.log('jsdjs')
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
    doStuff: function ()
    {
      this.addQueryToFilters();
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

      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          urlString = urlString + '&'+key+'=' + this.$route.query[key];
        }
      });
      if (this.$route.query.email !== undefined) {
      }

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
.list {
  @apply mt-5;
  display: inline-block;
}
.list_container {
  text-align: left;
  transition: height .4s ease;
  position: absolute;
  z-index: 1;
  background: white;

  @apply p-5 rounded-xl	border-slate-50 shadow-xl;
  float: left;
  right: 10px;
}
.list_container li {padding : 30px;}

.filter_field {
  @apply rounded-lg border-black p-2 bg-slate-50 border;
}
</style>