<template>
  <div class="text-center m-5 w-full content-center">
    <img src="/images/error-bear.png" alt="" class="m-auto" width="300" />
    <span class="m-auto text-xl dark:text-white">{{ $t(errorMessage) }}</span>
  </div>
</template>

<script>
export default {
  name: "ErrorBear",
  props: {
    errorMessage: {
      type: String,
      default() {
        return "app.default_error_message";
      }
    }
  },
}
</script>

<style scoped>

</style>
