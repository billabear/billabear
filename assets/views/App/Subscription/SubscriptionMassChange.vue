<template>
  <div>
    <h1 class="page-title">{{ $t('app.subscription.mass_change.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mt-5">

      </div>
      <div class="mt-5">
        <SubmitButton :in-progress="sendingRequest">{{ $t('app.subscription.mass_change.submit') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
export default {
  name: "SubscriptionMassChange",
  data() {
    return {
      ready: false,
      sendingRequest: false
    }
  }
}
</script>

<style scoped>

</style>