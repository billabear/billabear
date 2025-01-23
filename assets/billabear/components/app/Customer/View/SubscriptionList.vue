<template>
  <div>

    <div class="grid  grid-cols-1 md:grid-cols-2">
      <div><h2  class="section-header">{{ $t('app.customer.view.subscriptions.title') }}</h2></div>
      <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
        <div class="text-end"><router-link :to="{name: 'app.subscription.create', params: {customerId: customer.id}}" class="btn--main">{{ $t('app.customer.view.subscriptions.add_new') }}</router-link></div>

      </RoleOnlyView>
    </div>

    <div class="mt-2">

      <table class="list-table">
        <thead>
        <tr>
          <th>{{ $t('app.customer.view.subscriptions.list.plan_name') }}</th>
          <th>{{ $t('app.customer.view.subscriptions.list.status') }}</th>
          <th>{{ $t('app.customer.view.subscriptions.list.schedule') }}</th>
          <th>{{ $t('app.customer.view.subscriptions.list.valid_until') }}</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="subscription in subscriptions" class="mt-5">
          <td v-if="subscription.plan !== undefined && subscription.plan !== null">{{ subscription.plan.name }}</td>
          <td v-else>N/A</td>
          <td>{{ subscription.status }}</td>
          <td>{{ subscription.schedule }}</td>
          <td>{{ $filters.moment(subscription.valid_until, "LLL") }}</td>
          <td><router-link :to="{name: 'app.subscription.view', params: {subscriptionId: subscription.id}}" class="btn--main">{{ $t('app.customer.view.subscriptions.list.view') }}</router-link></td>
        </tr>
        <tr v-if="subscriptions.length == 0">
          <td colspan="6" class="text-center">{{ $t('app.customer.view.subscriptions.no_subscriptions') }}</td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="grid grid-cols-2 mt-2">
      <div class="text-left"><button class="btn--main" v-if="show_back" @click="fetchPrevPage">{{ $t('app.customer.view.invoices.prev') }}</button></div>
      <div class="text-end"><button class="btn--main" v-if="show_next" @click="fetchNextPage">{{ $t('app.customer.view.invoices.next') }}</button></div>
    </div>
  </div>
</template>

<script>
import RoleOnlyView from "../../RoleOnlyView.vue";
import currency from "currency.js";
import axios from "axios";

export default {
  name: "SubscriptionList",
  components: {RoleOnlyView},
  props: {
    resultSet: {
      type: Object,
    },
    customer: {
      type: Object
    }
  },
  data() {
    return {
      fetechedResult: null,
      show_back: false,
      fetched_show_next: null,
    }
  },
  computed: {
    subscriptions: function () {
      if (this.fetechedResult === null) {
        return this.resultSet.data;
      }
      return this.fetechedResult.data;
    },
    show_next: function() {
      if (this.fetched_show_next !== null) {
        return this.fetched_show_next;
      }

      if (this.fetechedResult === null) {
        return this.resultSet.has_more;
      }
      return this.fetechedResult.has_more;
    },
    last_key: function() {
      if (this.fetechedResult === null) {
        return this.resultSet.last_key;
      }
      return this.fetechedResult.last_key;
    },
    first_key: function() {
      if (this.fetechedResult === null) {
        return this.resultSet.first_key;
      }
      return this.fetechedResult.first_key;
    }
  },
  methods: {
    currency: function (value) {
      return currency(value, {fromCents: true});
    },
    fetchNextPage: function () {
      let urlString = '/app/subscription?customer=' + this.customer.id + '&last_key=' + encodeURIComponent(this.last_key);
      this.show_back = true;
      this.fetched_show_next = null;
      this.processRequest(urlString, 'normal')
    },
    fetchPrevPage: function () {
      let urlString = '/app/subscription?customer=' + this.customer.id + '&first_key=' + encodeURIComponent(this.first_key);
      this.fetched_show_next = true;
      this.processRequest(urlString, 'abnormal')
    },
    processRequest: function (urlString, mode) {
      axios.get(urlString).then(response => {
        this.fetechedResult = response.data;
        if (mode !== 'normal') { // Who knows what I was thinking when I named this.
          this.show_back = response.data.has_more;
          this.has_more = !response.data.has_more;
        }
        this.ready = true;
        this.loaded = true;
      }).catch(error => {
        this.has_error = true;
      })
    }
  }
}
</script>

<style scoped>

</style>
