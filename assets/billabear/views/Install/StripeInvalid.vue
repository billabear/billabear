<template>

  <div class="flex items-center justify-center h-screen login">
    <div class="p-5 public-form-body">
      <div class="w-full">
        <PublicLogo />
      </div>
      <p class="my-4 text-center">{{ $t('install.stripe.invalid_api_key') }}</p>

      <p class="my-4 text-center"><a target="_blank" :href="'https://docs.billabear.com/docs/technical/install/?utm_source=' + origin + '&utm_campaign=billabear_doc_links&utm_medium=update_announcement'">{{ $t('install.stripe.doc_link') }}</a></p>

      <p class="my-4 text-center"><a target="_blank" href="https://github.com/billabear/billabear/discussions/categories/q-a">{{ $t('install.stripe.support_link') }}</a></p>
    </div>
  </div>
</template>

<script>
import PublicLogo from "../../components/public/PublicLogo.vue";

export default {
  name: "StripeInvalid",
  components: {PublicLogo},
  data() {
    return {
      origin: '',
    }
  },
  mounted() {
    this.origin = window.location.hostname;
  }
}
</script>

<style scoped>

</style>