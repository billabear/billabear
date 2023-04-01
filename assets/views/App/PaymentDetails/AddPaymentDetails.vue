<template>
  <div>
    <h1 class="page-title">{{ $t('app.payment_details.add.title') }}</h1>
    <stripe-token-form :customer-id="customerId" />
  </div>
</template>

<script>
import StripeTokenForm from "../../../components/app/Billing/Stripe/StripeTokenForm.vue";

export default {
  name: "AddPaymentDetails",
  components: {StripeTokenForm},
  data() {
    return {
      customerId: null,
    }
  },
  beforeMount() {
    this.customerId = this.$route.params.customerId;
  }
}
</script>

<style scoped>

</style>