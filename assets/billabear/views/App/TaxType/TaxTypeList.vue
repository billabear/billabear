<template>
  <div v-if="!has_error">
    <div class="grid grid-cols-2">
      <h1 class="ml-5 mt-5 page-title">{{ $t('app.tax_type.list.title') }}</h1>

      <div class="mt-5 text-end">
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <router-link :to="{name: 'app.finance.tax_type.create'}" class="btn--main ml-4"><i class="fa-solid fa-plus"></i> {{ $t('app.tax_type.list.create_new') }}</router-link>
        </RoleOnlyView>
      </div>
    </div>


    <LoadingScreen :ready="ready">
      <div class="flex">
        <FiltersSection :filters="filters"/>
        <div class="pl-5 flex-1">

          <div class="rounded-lg bg-white shadow p-3">
            <table class="w-full">
              <thead>
              <tr class="border-b border-black">
              <th class="text-left pb-2">{{ $t('app.tax_type.list.list.name') }}</th>
              <th class="text-left pb-2">{{ $t('app.tax_type.list.list.default') }}</th>
                <th></th>
            </tr>
          </thead>
          <tbody v-if="loaded">
            <tr v-for="tax_type in taxTypes" class="mt-5 cursor-pointer">
              <td class="py-3">{{ tax_type.name }}</td>
              <td class="py-3">
                <span v-if="tax_type.default">{{ $t('app.tax_type.list.list.is_default') }}</span>
                <SubmitButton class="btn--main" :in-progress="sendingDefault" @click="makeDefault(tax_type.id)" v-else>{{ $t('app.tax_type.list.list.make_default') }}</SubmitButton>
              </td>
              <td><router-link class="btn--main" :to="{name: 'app.finance.tax_type.update', params: {id: tax_type.id}}">{{ $t('app.tax_type.list.list.update') }}</router-link></td>
            </tr>
            <tr v-if="taxTypes.length === 0">
              <td colspan="5" class="py-3 text-center">{{ $t('app.tax_type.list.no_tax_types') }}</td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr v-for="tax_type in taxTypes">
              <td colspan="5" class="py-3 text-center">
                <LoadingMessage>{{ $t('app.tax_type.list.loading') }}</LoadingMessage>
              </td>
            </tr>
          </tbody>
        </table>
    </div>
      <div class="sm:grid sm:grid-cols-2">

        <div class="mt-4">
          <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.tax_type.list.prev') }}</button>
          <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.tax_type.list.next') }}</button>
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
    {{ $t('app.tax_type.list.error_message') }}
  </div>
</template>

<script>
import axios from "axios";
import InternalApp from "../InternalApp.vue";
import "currency.js"
import currency from "currency.js";
import {Dropdown, ListGroup, ListGroupItem} from "flowbite-vue";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";
import FiltersSection from "../../../components/app/Ui/Section/FiltersSection.vue";

export default {
  name: "TaxTypeList.vue",
  components: {FiltersSection, ListGroupItem, ListGroup, Dropdown, InternalApp, RoleOnlyView},
  data() {
    return {
      ready: false,
      taxTypes: [],
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
      filters: {
      },
      sendingDefault: false,
    }
  },
  mounted() {
    this.loadTaxTypes();

  },
  watch: {
    '$route.query': function (id) {
      this.loadTaxTypes()
    }
  },
  methods: {
    makeDefault: function (taxTypeId){
      this.sendingDefault = true;
      axios.post('/app/tax/type/'+taxTypeId+'/default').then(response => {
        this.taxTypes = response.data.data;
        this.sendingDefault =false;
      })
    },
    currency: function (value) {
        return currency(value, { fromCents: true });
    },
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
    loadTaxTypes: function ()
    {
      this.syncQueryToFilters();
      var mode = 'normal';
      let urlString = '/app/tax/type?';
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

        this.taxTypes = response.data.data;
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
.list {
  @apply mt-5;
  display: inline-block;
}

.filter_field {
  @apply rounded-lg border-black p-2 bg-slate-50 border;
}
</style>
