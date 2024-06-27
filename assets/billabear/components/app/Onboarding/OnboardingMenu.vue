<template>
  <div
      class="rounded-xl p-5 my-5 border-2 border-gray-900 bg-white dark:bg-gray-800 dark:border-gray-300 dark:text-gray-200">
    <h2 class="text-2xl font-bold mb-5">{{ $t('app.onboarding.main.dialog.title') }}</h2>

    <ul>
      <li>
        <svg v-if="!has_stripe_key" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             height="50" width="50" class="size-6 text-red-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                clip-rule="evenodd"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="50" width="50"
             class="size-6 text-green-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                clip-rule="evenodd"/>
        </svg>

        <span>{{ $t('app.onboarding.main.dialog.has_stripe_key.text') }}</span>

        <router-link class="ml-3 btn--black" :to="{name: 'app.settings.import.stripe'}" v-if="!has_stripe_key">
          {{ $t('app.onboarding.main.dialog.has_stripe_key.button') }}
        </router-link>
      </li>


      <li>
        <svg v-if="!has_stripe_imports" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             height="50" width="50" class="size-6 text-red-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                clip-rule="evenodd"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="50" width="50"
             class="size-6 text-green-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                clip-rule="evenodd"/>
        </svg>

        <span>{{ $t('app.onboarding.main.dialog.has_stripe_imports.text') }}</span>

        <router-link class="ml-3 btn--black" :to="{name: 'app.settings.import.stripe'}" v-if="!has_stripe_imports">
          {{ $t('app.onboarding.main.dialog.has_stripe_imports.button') }}
        </router-link>
        <span class="text-blue-500 underline cursor-pointer ml-3" @click="dismissStripeImport" v-if="!has_stripe_imports">
          {{ $t('app.onboarding.main.dialog.has_stripe_imports.dismiss') }}
        </span>
      </li>


      <li>
        <svg v-if="!has_product" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             height="50" width="50" class="size-6 text-red-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                clip-rule="evenodd"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="50" width="50"
             class="size-6 text-green-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                clip-rule="evenodd"/>
        </svg>

        <span>{{ $t('app.onboarding.main.dialog.has_product.text') }}</span>

        <router-link class="ml-3 btn--black" :to="{name: 'app.product.create'}" v-if="!has_product">
          {{ $t('app.onboarding.main.dialog.has_product.button') }}
        </router-link>
      </li>

      <li>
        <svg v-if="!has_subscription_plan" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             height="50" width="50" class="size-6 text-red-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                clip-rule="evenodd"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="50" width="50"
             class="size-6 text-green-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                clip-rule="evenodd"/>
        </svg>

        <span>{{ $t('app.onboarding.main.dialog.has_subscription_plan.text') }}</span>

      </li>

      <li>
        <svg v-if="!has_customer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             height="50" width="50" class="size-6 text-red-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                clip-rule="evenodd"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="50" width="50"
             class="size-6 text-green-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                clip-rule="evenodd"/>
        </svg>

        <span>{{ $t('app.onboarding.main.dialog.has_customer.text') }}</span>

        <router-link class="ml-3 btn--black" :to="{name: 'app.customer.create'}" v-if="!has_customer">
          {{ $t('app.onboarding.main.dialog.has_customer.button') }}
        </router-link>
      </li>

      <li>
        <svg v-if="!has_subscription" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
             height="50" width="50" class="size-6 text-red-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                clip-rule="evenodd"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" height="50" width="50"
             class="size-6 text-green-500 inline">
          <path fill-rule="evenodd"
                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                clip-rule="evenodd"/>
        </svg>

        <span>{{ $t('app.onboarding.main.dialog.has_subscription.text') }}</span>

      </li>


    </ul>

  </div>
</template>

<script>
import {mapActions, mapState} from "vuex";

export default {
  name: "OnboardingMenu",

  computed: {
    ...mapState('onboardingStore', [
      'has_stripe_key',
      'has_stripe_imports',
      'has_subscription_plan',
      'has_customer',
      'has_subscription',
      'has_product',
      'show_onboarding'
    ])
  },
  methods: {
    ...mapActions('onboardingStore', ['dismissStripeImport'])
  }
}
</script>


<style scoped>

</style>
