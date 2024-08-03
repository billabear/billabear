<template>
  <div v-if="!has_error">
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.invoices.list.unpaid_title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="flex">
        <FiltersSection :filters="filters"/>
        <div class="pl-5 flex-1">

          <div class="rounded-lg bg-white shadow p-3">
            <table class="w-full">
          <thead>
            <tr class="border-b border-black">
              <th class="text-left pb-2">{{ $t('app.invoices.list.email') }}</th>
              <th class="text-left pb-2">{{ $t('app.invoices.list.total')}}</th>
              <th class="text-left pb-2">{{ $t('app.invoices.list.currency')}}</th>
              <th class="text-left pb-2">{{ $t('app.invoices.list.created_at') }}</th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody v-if="loaded">
            <tr v-for="invoice in invoices" class="mt-5">
              <td class="py-3">{{ invoice.customer.email }}</td>
              <td class="py-3">{{ currency(invoice.total) }}</td>
              <td class="py-3">{{ invoice.currency }}</td>
              <td class="py-3">{{ $filters.moment(invoice.created_at, "LLL")}}</td>
              <td class="py-3"><router-link :to="{name: 'app.invoices.view', params: {id: invoice.id}}" class="list-btn">{{ $t('app.invoices.list.view_btn') }}</router-link></td>
              <td class="py-3">
                <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT" v-if="invoice.customer.billing_type == 'card' && invoice.paid == false">
                  <SubmitButton button-class="ml-3 btn--secondary--main" :in-progress="charging_invoice" @click="attemptPayment(invoice)" >{{ $t('app.invoices.list.charge') }}</SubmitButton>
                  <button class="btn--secondary--menu" @click="showMenu(invoice)"><i class="fa-solid fa-caret-down"></i></button>
                  <div id="dropdown-menu" class="absolute w-40 bg-white rounded-md ml-3 shadow-lg z-10" :class="{hidden: invoice.show_menu !== true}">
                    <a @click="markAsPaid(invoice)" class="cursor-pointer block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white">{{ $t('app.invoices.list.mark_as_paid') }}</a>
                    <!-- Add more dropdown options as needed -->
                  </div>
                </RoleOnlyView>
              </td>
            </tr>
            <tr v-if="invoices.length === 0">
              <td colspan="4" class="text-center">{{ $t('app.invoices.list.no_invoices') }}</td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr v-for="invoice in invoices">
              <td colspan="4" class="py-3 text-center">
                <LoadingMessage>{{ $t('app.invoices.list.loading') }}</LoadingMessage>
              </td>
            </tr>
          </tbody>
        </table>
    </div>
      <div class="sm:grid sm:grid-cols-2">

        <div class="mt-4">
          <button @click="prevPage" v-if="show_back" class="btn--main mr-3" >{{ $t('app.invoices.list.prev') }}</button>
          <button @click="nextPage" v-if="has_more" class="btn--main" >{{ $t('app.invoices.list.next') }}</button>
        </div>
        <div class="mt-4 text-end">
          <select  class="rounded-lg border border-gray-300" @change="changePerPage" v-model="per_page">
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
    {{ $t('app.invoices.list.error_message') }}
  </div>
</template>

<script>
import axios from "axios";
import InternalApp from "../InternalApp.vue";
import currency from "currency.js";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";
import {Dropdown, ListGroup, ListGroupItem} from "flowbite-vue";
import FiltersSection from "../../../components/app/Ui/Section/FiltersSection.vue";

export default {
  name: "InvoiceList.vue",
  components: {FiltersSection, ListGroupItem, ListGroup, Dropdown, RoleOnlyView, InternalApp},
  data() {
    return {
      ready: false,
      loaded: false,
      has_error: false,
      invoices: [],
      has_more: false,
      last_key: null,
      first_key: null,
      previous_last_key: null,
      next_page_in_progress: false,
      show_back: false,
      show_filter_menu: false,
      active_filters: [],
      charging_invoice: false,
      per_page: "10",
      filters: {
        email: {
          label: 'app.invoices.list.filter.email',
          type: 'text',
          value: null
        },
        reference: {
          label: 'app.invoices.list.filter.number',
          type: 'text',
          value: null
        },
      }
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
    markAsPaid: function (invoice) {
      this.charging_invoice = true;
      axios.post('/app/invoice/'+invoice.id+'/paid').then(response => {
        invoice.paid = true
        this.charging_invoice = false;
      })
    },
    showMenu: function (invoice) {
        invoice.show_menu = !invoice.show_menu;

    },
    attemptPayment: function (invoice ) {
      this.charging_invoice = true;
      axios.post('/app/invoice/'+invoice.id+'/charge').then(response => {
        invoice.paid = response.data.paid;
        this.charging_invoice = false;
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
      let urlString = '/app/invoices/unpaid?';
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

        this.invoices = response.data.data;
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
