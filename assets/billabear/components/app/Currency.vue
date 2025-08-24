<template>
  <span v-if="currency != ''" class="dark:text-gray-300">{{ currency }}</span> <span class=" dark:text-gray-300">{{ displayCurrency(amount) }}</span>
</template>

<script setup>
import currency from "currency.js";

const props = defineProps({
  amount: {
    type: Number,
    default: 0,
  },
  currency: {
    type: String,
    default: "",
  }
});

const displayCurrency = (value) => {
  return currency(value, { fromCents: true }).format({symbol: ''});
};
</script>

<style scoped>

</style>
