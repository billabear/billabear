<template>
  <span v-if="currency != ''" class=" dark:text-gray-300">{{ currency }}</span> <span class=" dark:text-gray-300">{{ displayCurrency(amount) }}</span>
</template>

<script>
import currency from "currency.js";

export default {
  name: "Currency",
  props: {
      amount: {
        type: Number,
        default() {
          return 0;
        },
      },
      currency: {
        type: String,
        default() {
          return "";
        }
      }
    },
  methods: {

    displayCurrency: function (value) {
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
  }
}
</script>

<style scoped>

</style>