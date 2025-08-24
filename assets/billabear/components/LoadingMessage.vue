<template>
  <div class="flex items-center justify-center py-2">
    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-900 mr-2"></div>
    <span class="text-gray-600">
      <slot>Loading...</slot>
    </span>
  </div>
</template>

<script>
export default {
  name: "LoadingMessage"
}
</script>

<style scoped>
</style>