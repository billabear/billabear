<template>
  <div>
    <div class="grid grid-cols-2">
      <h1 class="page-title">{{ $t('app.workflows.charge_back_creation.list.title') }}</h1>

      <div class="text-end m-5">

        <RoleOnlyView role="ROLE_DEVELOPER">
          <router-link :to="{name:'app.workflows.charge_back_creation.edit'}" class="btn--main btn--secondary mr-5 p-5">
            {{ $t('app.workflows.charge_back_creation.list.edit_button') }}
          </router-link>
          <SubmitButton :in-progress="bulk_in_progress" class="btn--main mr-5" @click="bulk">
            {{ $t('app.workflows.charge_back_creation.list.bulk_button') }}
          </SubmitButton>
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
            <th class="text-left pb-2">{{ $t('app.workflows.charge_back_creation.list.email') }}</th>
            <th class="text-left pb-2">{{ $t('app.workflows.charge_back_creation.list.status') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody v-if="loaded">
          <tr v-for="subscription in subscriptions" class="mt-5">
            <td class="py-3">{{ subscription.payment.customer.email }}</td>
            <td class="py-3">{{ subscription.state }}</td>
            <td class="py-3"><router-link :to="{name: 'app.workflows.charge_back_creation.view', params: {id: subscription.id}}" class="btn--main">{{ $t('app.workflows.charge_back_creation.list.view') }}</router-link></td>
          </tr>
          <tr v-if="subscriptions.length === 0">
            <td colspan="4" class="py-3 text-center">{{ $t('app.workflows.charge_back_creation.list.no_results') }}</td>
          </tr>
          </tbody>
          <tbody v-else>
          <tr v-for="subscription in subscriptions">
            <td colspan="4" class="py-3 text-center">
              <LoadingMessage>{{ $t('app.workflows.payment_creation.list.loading') }}</LoadingMessage>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="sm:grid sm:grid-cols-2">

        <div class="m-5">
          <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.workflows.charge_back_creation.list.prev') }}</button>
          <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.workflows.charge_back_creation.list.next') }}</button>
        </div>
        <div class="mt-4 text-end">
          <select class="rounded-lg border border-gray-300"  @change="changePerPage" v-model="per_page">
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
</template>

<script>
import axios from "axios";
import {Dropdown, Input, ListGroup, ListGroupItem} from "flowbite-vue";
import RoleOnlyView from "../../../../components/app/RoleOnlyView.vue";
import FiltersSection from "../../../../components/app/Ui/Section/FiltersSection.vue";

export default {
  name: "ChargeBackCreationList",
  components: {FiltersSection, RoleOnlyView, Input, Dropdown, ListGroupItem, ListGroup},
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
      active_filters: ['has_error'],
      per_page: "10",
      filters: {
        has_error: {
          label: 'app.workflows.cancellation_request.list.filter.has_error',
          type: 'boolean',
          value: true,
        },
      }
    }
  },
  mounted() {

    const queryVals = this.buildFilterQuery();
    this.$router.push({query: queryVals})
    this.loadChargeBackCreations();

  },
  watch: {
    '$route.query': function (id) {
      this.loadChargeBackCreations()
    }
  },
  methods: {
    bulk: function () {
      this.bulk_in_progress=true;
      axios.post('/app/system/charge-back-creation/bulk').then(response => {

        this.bulk_in_progress=false;
      }).catch(error => {
        this.bulk_in_progress=false;
      })
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
    loadChargeBackCreations: function ()
    {
      this.syncQueryToFilters();
      const mode = 'normal';
      let urlString = '/app/system/charge-back-creation/list?';

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
