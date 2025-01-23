<template>
  <input type="text"
         class="rounded-lg border border-gray-300 ring-teal-500"
         :value="$attrs.modelValue"
         @change="$emit('update:modelValue', $event.target.value)"  />
</template>

<script>
export default {
  name: "InputText"
}
</script>

<style scoped>

</style>
