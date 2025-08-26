<template>
  <div>
    <h1 class="page-title">{{ $t('app.payment_details.add.title') }}</h1>
    <stripe-token-form :customer-id="customerId" />
  </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue'
import { useRoute } from 'vue-router'
import StripeTokenForm from '../../../components/app/Billing/Stripe/StripeTokenForm.vue'

// Route access
const route = useRoute()

// Reactive data
const customerId = ref(null)

// Lifecycle hook
onBeforeMount(() => {
  customerId.value = route.params.customerId
})
</script>

<style scoped></style>
