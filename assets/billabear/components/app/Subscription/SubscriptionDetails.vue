<template>
  <div class="card-body">
    <h2 class="section-header">{{ $t('app.subscription.view.main.title') }}</h2>
    <dl class="detail-list section-body">
      <div>
        <dt>{{ $t('app.subscription.view.main.status') }}</dt>
        <dd>{{ subscription.status }}</dd>
      </div>

      <div v-if="subscription.plan !== null && subscription.plan !== undefined">
        <dt>{{ $t('app.subscription.view.main.plan') }}</dt>
        <dd>
          <router-link :to="{name: 'app.subscription_plan.view', params: {productId: product.id, subscriptionPlanId: subscription.plan.id}}">
            {{ subscription.plan.name }}
          </router-link>
          <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
            <button class="btn--main ml-3" @click="$emit('show-plan')">{{ $t('app.subscription.view.main.plan_change') }}</button>
          </RoleOnlyView>
        </dd>
      </div>

      <div>
        <dt>{{ $t('app.subscription.view.main.customer') }}</dt>
        <dd>
          <router-link :to="{name: 'app.customer.view', params: {id: customer.id}}">
            {{ customer.email }}
          </router-link>
        </dd>
      </div>

      <div>
        <dt>{{ $t('app.subscription.view.main.main_external_reference') }}</dt>
        <dd>
          <a v-if="subscription.external_main_reference_details_url" target="_blank" :href="subscription.external_main_reference_details_url">
            {{ subscription.main_external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
          </a>
          <span v-else>{{ subscription.main_external_reference }}</span>
        </dd>
      </div>

      <div>
        <dt>{{ $t('app.subscription.view.main.created_at') }}</dt>
        <dd>{{ $filters.moment(subscription.created_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}</dd>
      </div>

      <div v-if="subscription.ended_at != null">
        <dt>{{ $t('app.subscription.view.main.ended_at') }}</dt>
        <dd>{{ $filters.moment(subscription.ended_at, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}</dd>
      </div>
      <div v-else>
        <dt>{{ $t('app.subscription.view.main.valid_until') }}</dt>
        <dd>{{ $filters.moment(subscription.valid_until, "dddd, MMMM Do YYYY, h:mm:ss a") || "unknown" }}</dd>
      </div>

      <div v-if="subscription.plan.per_seat == true">
        <dt>{{ $t('app.subscription.view.main.seat_number') }}</dt>
        <dd>
          {{ subscription.seat_number }}
          <RoleOnlyView role="ROLE_CUSTOMER_SUPPORT">
            <button class="btn--main ml-3" @click="$emit('show-seat-change')">{{ $t('app.subscription.view.main.change_seat') }}</button>
          </RoleOnlyView>
        </dd>
      </div>
    </dl>
  </div>
</template>

<script>
import RoleOnlyView from "../../RoleOnlyView.vue";

export default {
  name: "SubscriptionDetails",
  components: {
    RoleOnlyView
  },
  props: {
    subscription: {
      type: Object,
      required: true
    },
    customer: {
      type: Object,
      required: true
    },
    product: {
      type: Object,
      required: true
    }
  },
  emits: ['show-plan', 'show-seat-change']
}
</script>