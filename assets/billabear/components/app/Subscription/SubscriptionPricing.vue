<template>
  <div class="card-body">
    <h2 class="section-header">{{ $t('app.subscription.view.pricing.title') }}</h2>
    <dl class="detail-list section-body" v-if="subscription.price">
      <div>
        <dt>{{ $t('app.subscription.view.pricing.price') }}</dt>
        <dd>{{ subscription.price.display_value }}</dd>
      </div>
      <div>
        <dt>{{ $t('app.subscription.view.pricing.recurring') }}</dt>
        <dd>{{ subscription.price.recurring }}</dd>
      </div>
      <div v-if="subscription.price.recurring">
        <dt>{{ $t('app.subscription.view.pricing.schedule') }}</dt>
        <dd>{{ subscription.price.schedule }}</dd>
      </div>
    </dl>
    <span class="text-center w-full" v-else>{{ $t('app.subscription.view.pricing.no_price') }}</span>
    <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
      <div class="mt-2">
        <button class="btn--container" @click="$emit('show-price')">{{ $t('app.subscription.view.pricing.change') }}</button>
      </div>
    </RoleOnlyView>
  </div>
</template>

<script>
import RoleOnlyView from "../RoleOnlyView.vue";

export default {
  name: "SubscriptionPricing",
  components: {
    RoleOnlyView
  },
  props: {
    subscription: {
      type: Object,
      required: true
    }
  },
  emits: ['show-price']
}
</script>