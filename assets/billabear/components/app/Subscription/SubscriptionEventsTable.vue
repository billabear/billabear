<template>
  <div class="card-body">
    <div class="grid grid-cols-2">
      <div>
        <h2 class="section-header">{{ $t('app.subscription.view.subscription_events.title') }}</h2>
      </div>
    </div>

    <div class="mt-2">
      <table class="list-table">
        <thead>
          <tr>
            <th>{{ $t('app.subscription.view.subscription_events.list.event') }}</th>
            <th>{{ $t('app.subscription.view.subscription_events.list.created_at') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="subscription in subscriptionEvents" :key="subscription.id" class="mt-5">
            <td>{{ subscription.type }}</td>
            <td>{{ $filters.moment(subscription.created_at, "LLL") }}</td>
          </tr>
          <tr v-if="subscriptionEvents.length == 0">
            <td colspan="6" class="text-center">{{ $t('app.subscription.view.subscription_events.no_subscription_events') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "SubscriptionEventsTable",
  props: {
    subscriptionEvents: {
      type: Array,
      required: true,
      default: () => []
    }
  }
}
</script>