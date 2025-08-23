<template>
  <div v-if="!has_error">
    <div class="grid grid-cols-2">

      <h1 class="ml-5 mt-5 page-title">{{ $t('app.subscription.mass_change.list.title') }}</h1>

      <div class="text-end mt-5">
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <router-link :to="{name: 'app.subscription.mass_change.create'}" class="btn--main ml-4"><i class="fa-solid fa-plus"></i> {{ $t('app.subscription.mass_change.list.create_new') }}</router-link>
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
            <th class="text-left pb-2">{{ $t('app.subscription.mass_change.list.change_date') }}</th>
            <th class="text-left pb-2">{{ $t('app.subscription.mass_change.list.status') }}</th>
            <th class="text-left pb-2">{{ $t('app.subscription.mass_change.list.created_at') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody v-if="loaded">
          <tr v-for="subscription in subscriptions" class="mt-5">
            <td class="py-3">{{ subscription.change_date }}</td>
            <td>{{ subscription.status }}</td>
            <td>{{ $filters.moment(subscription.created_at, 'lll') }}</td>
            <td><router-link :to="{name: 'app.subscription.mass_change.view', params: {id: subscription.id}}" class="btn--main">{{ $t('app.subscription.list.view') }}</router-link></td>
          </tr>
          <tr v-if="subscriptions.length === 0">
            <td colspan="4" class="py-3 text-center">{{ $t('app.subscription.mass_change.list.no_mass_change') }}</td>
          </tr>
          </tbody>
          <tbody v-else>
          <tr v-for="subscription in subscriptions">
            <td colspan="4" class="text-center">
              <LoadingMessage>{{ $t('app.subscription.mass_change.list.loading') }}</LoadingMessage>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="sm:grid sm:grid-cols-2 mt-5">

        <div class="mt-4">
          <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.subscription.mass_change.list.prev') }}</button>
          <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.subscription.mass_change.list.next') }}</button>
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
    {{ $t('app.subscription.mass_change.list.error_message') }}
  </div>
</template>

<script>
import axios from "axios";
import FiltersSection from "../../../../components/app/Ui/Section/FiltersSection.vue";

export default {
  name: "MassChanteList",
  components: {FiltersSection},
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
    this.loadMassChanges();

  },
  watch: {
    '$route.query': function (id) {
      this.loadMassChanges()
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
            this.active_filters.splice( this.active_filters.indexOf(key) , 1) ;
          }
        }
      });
    },
    doSearch: function () {
      const queryVals = this.buildFilterQuery();
      this.$router.push({query: queryVals})
    },
    buildFilterQuery: function () {
      const queryVals = {};
      for (let i = 0; i < this.active_filters.length; i++) {
        const filter = this.active_filters[i];
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

      const queryVals = this.buildFilterQuery();
      queryVals.last_key = this.last_key;
      this.$router.push({query: queryVals})
    },
    prevPage: function () {
      const queryVals = this.buildFilterQuery();
      queryVals.first_key = this.first_key;
      this.$router.push({query: queryVals})
    },
    changePerPage: function ($event) {
      const queryVals = this.buildFilterQuery();
      queryVals.per_page = $event.target.value;
      this.per_page=queryVals.per_page;

      if (this.$route.query.last_key !== undefined) {
        queryVals.last_key = this.$route.query.last_key;
      } else if (this.$route.query.first_key !== undefined) {
        queryVals.first_key = this.$route.query.first_key;
      }

      this.$router.push({query: queryVals});
    },
    loadMassChanges: function ()
    {
      this.syncQueryToFilters();
      const mode = 'normal';
      let urlString = '/app/subscription/mass-change?';

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
      const newFilters = [];
      let found = false;
      for (let i = 0; i < this.active_filters.length; i++) {
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
      for (let i = 0; i < this.active_filters.length; i++) {
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
