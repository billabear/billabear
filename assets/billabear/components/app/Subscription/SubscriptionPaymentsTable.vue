<template>
  <div class="card-body">
    <h2 class="section-header">{{ $t('app.subscription.view.payments.title') }}</h2>
    <div class="section-body">
      <table class="list-table">
        <thead>
          <tr>
            <th>{{ $t('app.subscription.view.payments.amount') }}</th>
            <th>{{ $t('app.subscription.view.payments.created_at') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="payments.length == 0">
            <td colspan="3" class="text-center">{{ $t('app.subscription.view.payments.no_payments') }}</td>
          </tr>
          <tr v-for="payment in payments" :key="payment.id">
            <td>{{ currency(payment.amount) }}</td>
            <td>{{ $filters.moment(payment.created_at, 'lll') }}</td>
            <td>
              <router-link :to="{name: 'app.payment.view', params: {id: payment.id}}" class="btn--main">
                {{ $t('app.subscription.view.payments.view') }}
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import currency from "currency.js";

export default {
  name: "SubscriptionPaymentsTable",
  props: {
    payments: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  methods: {
    currency(amount) {
      return currency(amount).format();
    }
  }
}
</script>