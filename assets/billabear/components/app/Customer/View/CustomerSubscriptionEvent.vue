<template>
  <div>
    <div class="grid grid-cols-2">
      <div><h2  class="section-header">{{ $t('app.customer.view.subscription_events.title') }}</h2></div>
    </div>

    <div class="mt-2">

      <table class="list-table">
        <thead>
        <tr>
          <th>{{ $t('app.customer.view.subscription_events.list.event') }}</th>
          <th>{{ $t('app.customer.view.subscription_events.list.subscription') }}</th>
          <th>{{ $t('app.customer.view.subscription_events.list.created_at') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="subscription in subscription_events" class="mt-5">
          <td>{{ subscription.type }}</td>
          <td><router-link :to="{name: 'app.subscription.view', params: {subscriptionId: subscription.id}}">{{ subscription.subscription.plan.name }}</router-link></td>
          <td>{{ $filters.moment(subscription.created_at, "LLL") }}</td>
        </tr>
        <tr v-if="subscription_events.length == 0">
          <td colspan="6" class="text-center">{{ $t('app.customer.view.subscription_events.no_subscription_events') }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "CustomerSubscriptionEvent",
  props: {
    subscription_events: {
      type: Array,
    }
  }
}
</script>

<style scoped>

</style>
