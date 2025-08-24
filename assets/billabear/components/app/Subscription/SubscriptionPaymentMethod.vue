<template>
  <div class="card-body">
    <h2 class="section-header">{{ $t('app.subscription.view.payment_method.title') }}</h2>
    <dl class="detail-list section-body" v-if="paymentDetails !== null && paymentDetails !== undefined">
      <div>
        <dt>{{ $t('app.subscription.view.payment_method.last_four') }}</dt>
        <dd>**** **** **** {{ paymentDetails.last_four }}</dd>
      </div>
      <div>
        <dt>{{ $t('app.subscription.view.payment_method.brand') }}</dt>
        <dd>{{ paymentDetails.brand }}</dd>
      </div>
      <div>
        <dt>{{ $t('app.subscription.view.payment_method.expiry_month') }}</dt>
        <dd>{{ paymentDetails.expiry_month }}</dd>
      </div>
      <div>
        <dt>{{ $t('app.subscription.view.payment_method.expiry_year') }}</dt>
        <dd>{{ paymentDetails.expiry_year }}</dd>
      </div>
    </dl>
    <div v-else class="text-center section-body">
      {{ $t('app.subscription.view.payment_method.invoiced') }}
    </div>
  </div>
</template>

<script>
export default {
  name: "SubscriptionPaymentMethod",
  props: {
    paymentDetails: {
      type: Object,
      default: null
    }
  }
}
</script>