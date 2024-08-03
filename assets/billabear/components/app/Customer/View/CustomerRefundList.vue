<template>
  <div>
    <h2 class="section-header">{{ $t('app.customer.view.refunds.title') }}</h2>
    <div class="">
      <table class="list-table">
        <thead>
        <tr>
          <th>{{ $t('app.customer.view.refunds.list.amount') }}</th>
          <th>{{ $t('app.customer.view.refunds.list.currency') }}</th>
          <th>{{ $t('app.customer.view.refunds.list.created_by') }}</th>
          <th>{{ $t('app.customer.view.refunds.list.created_at') }}</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="refund in refunds">
          <td>{{ currency(refund.amount) }}</td>
          <td>{{ refund.currency }}</td>
          <td v-if="refund.billing_admin != null">{{ refund.billing_admin.display_name }}</td>
          <td v-else>API</td>
          <td>{{ $filters.moment(refund.created_at, "LLL") || "unknown" }}</td>
        </tr>
        <tr v-if="refunds.length == 0">
          <td colspan="5" class="text-center">{{ $t('app.customer.view.refunds.no_refunds') }}</td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="grid grid-cols-2 mt-2">
      <div class="text-left">
        <button class="btn--main" v-if="show_back" @click="fetchPrevPage">{{
            $t('app.customer.view.invoices.prev')
          }}
        </button>
      </div>
      <div class="text-end">
        <button class="btn--main" v-if="show_next" @click="fetchNextPage">{{
            $t('app.customer.view.invoices.next')
          }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import RoleOnlyView from "../../RoleOnlyView.vue";
import currency from "currency.js";
import axios from "axios";

export default {
  name: "CustomerRefundList",
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
    refunds: function () {
      if (this.fetechedResult === null) {
        return this.resultSet.data;
      }
      return this.fetechedResult.data;
    },
    show_next: function () {
      if (this.fetched_show_next !== null) {
        return this.fetched_show_next;
      }

      if (this.fetechedResult === null) {
        return this.resultSet.has_more;
      }
      return this.fetechedResult.has_more;
    },
    last_key: function () {
      if (this.fetechedResult === null) {
        return this.resultSet.last_key;
      }
      return this.fetechedResult.last_key;
    },
    first_key: function () {
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
      let urlString = '/app/refund?customer=' + this.customer.id + '&last_key=' + encodeURIComponent(this.last_key);
      this.show_back = true;
      this.fetched_show_next = null;
      this.processRequest(urlString, 'normal')
    },
    fetchPrevPage: function () {
      let urlString = '/app/refund?customer=' + this.customer.id + '&first_key=' + encodeURIComponent(this.first_key);
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
