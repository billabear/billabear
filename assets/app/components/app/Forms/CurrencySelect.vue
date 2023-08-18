<template>
  <select v-model="localValue" @change="$emit('update:modelValue', $event.target.value)" class="form-field">
    <option>EUR</option>
    <option>USD</option>
    <option>GBP</option>
    <option>CAD</option>
    <option>AUD</option>
    <option>NOK</option>
    <option>DKK</option>
    <option>HUF</option>
    <option>SEK</option>
    <option>CHF</option>
    <option>CZK</option>
    <option>HKD</option>
    <option>JPY</option>
  </select>
</template>

<script>
export default {
  name: "CurrencySelect",
  props: ['modelValue'],
  data() {
    return {
      localValue: this.modelValue,
    }
  },
}
</script>

<style scoped>

</style>