<template>
  <div v-if="!has_error">
    <div class="grid grid-cols-2">

      <PageTitle>{{ $t('app.customer.list.title') }}</PageTitle>

      <div class="text-end mt-3">
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <router-link :to="{name: 'app.customer.create'}" class="btn--main ml-4"><i class="fa-solid fa-user-plus"></i> {{ $t('app.customer.list.create_new') }}</router-link>
        </RoleOnlyView>
      </div>
    </div>

    <LoadingScreen :ready="ready">
      <div class="md:flex">
        <FiltersSection :filters="filters" />
      <div class="md:pl-3 md:flex-1">

          <div class="rounded-lg bg-white shadow p-3">
            <table class="w-full">
              <thead>
              <tr class="border-b border-black">
                <th class="text-left pb-2">{{ $t('app.customer.list.email') }}</th>
                <th class="text-left pb-2">{{ $t('app.customer.list.company_name')}}</th>
                <th class="text-left pb-2">{{ $t('app.customer.list.country')}}</th>
                <th class="text-left pb-2">{{ $t('app.customer.list.reference') }}</th>
                <th></th>
              </tr>
              </thead>
              <tbody v-if="loaded">
              <tr v-for="customer in customers" class="cursor-pointer hover:bg-gray-50" @click="$router.push({name: 'app.customer.view', params: {id: customer.id}})">
                <td class="py-3">{{ customer.email }}</td>
                <td class="py-3">{{ customer.address.company_name }}</td>
                <td class="py-3">{{ customer.address.country }}</td>
                <td class="py-3">{{ customer.reference }}</td>
                <td class="py-3"><router-link :to="{name: 'app.customer.view', params: {id: customer.id}}" class="rounded-lg w-full p-2 bg-teal-500 text-white font-bold">{{ $t('app.customer.list.view_btn') }}</router-link></td>
              </tr>
              <tr v-if="customers.length === 0">
                <td colspan="4" class="text-center">{{ $t('app.customer.list.no_customers') }}</td>
              </tr>
              </tbody>
              <tbody v-else>
              <tr v-for="customer in customers">
                <td colspan="4" class="py-3 text-center">
                  <LoadingMessage>{{ $t('app.customer.list.loading') }}</LoadingMessage>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="sm:grid sm:grid-cols-2 m-2">

            <div class="mt-4">
              <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.customer.list.prev') }}</button>
              <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.customer.list.next') }}</button>
            </div>
            <div class="mt-4 text-end">
              <select class="rounded-lg border border-gray-300" @change="changePerPage" v-model="per_page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
          </div>
      </div>
    </div>
    </LoadingScreen>
  </div>
  <div v-else class="error-page">
    {{ $t('app.customer.list.error_message') }}
  </div>
</template>

<script>
import axios from "axios";
import InternalApp from "../InternalApp.vue";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";
import {Dropdown, ListGroup, ListGroupItem} from "flowbite-vue";
import PageTitle from "../../../components/app/Ui/Typography/PageTitle.vue";
import InputText from "../../../components/app/Ui/Forms/InputText.vue";
import FiltersSection from "../../../components/app/Ui/Section/FiltersSection.vue";

export default {
  name: "CustomerList.vue",
  components: {FiltersSection, InputText, PageTitle, ListGroupItem, ListGroup, Dropdown, RoleOnlyView, InternalApp},
  data() {
    return {
      ready: false,
      loaded: false,
      has_error: false,
      customers: [],
      has_more: false,
      last_key: null,
      first_key: null,
      previous_last_key: null,
      next_page_in_progress: false,
      show_back: false,
      show_filter_menu: false,
      active_filters: [],
      per_page: "10",
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
        },
        company_name: {
          label: 'app.customer.list.filter.company_name',
          type: 'text',
          value: null
        }
      }
    }
  },
  mounted() {
    this.loadCustomers();

  },
  watch: {
    '$route.query': function (id) {
      this.loadCustomers()
    }
  },
  methods: {
    syncQueryToFilters: function () {
      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          this.filters[key].value = this.$route.query[key];
        } else {
          this.filters[key].value = null;
        }
      });
    },
    doSearch: function () {
        var queryVals = this.buildFilterQuery();
        this.$router.push({query: queryVals})
    },
    buildFilterQuery: function () {
      var queryVals = {};
      for (var filter in this.filters) {
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
    loadCustomers: function ()
    {
      this.syncQueryToFilters();
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

      if (this.$route.query.per_page !== undefined) {
        urlString = urlString + '&per_page=' + this.$route.query.per_page;
      }

      Object.keys(this.filters).forEach(key => {
        if (this.$route.query[key] !== undefined) {
          urlString = urlString + '&'+key+'=' + this.$route.query[key];
        }
      });

      this.loaded = false;
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
