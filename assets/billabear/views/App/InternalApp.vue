<template>
  <div>
    <Topbar />
    <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">

      <Sidebar />

      <div id="main-content" class="relative w-full h-full bg-gray-50 lg:ml-64 dark:bg-gray-900">
        <main>
          <div class="">
            <div v-if="!has_stripe_key" class="w-100 bg-red-500 text-white p-3 font-bold">
              {{ $t('app.onboarding.main.bar.message') }}
            </div>
            <router-view></router-view>
          </div>
        </main>
      </div>
    </div>
  </div>

</template>

<script>
import AppLogo from "../../components/app/AppLogo.vue";
import axios from "axios";
import {mapActions, mapState} from "vuex";
import RoleOnlyView from "../../components/app/RoleOnlyView.vue";
import DarkMode from "../../components/app/DarkMode.vue";
import Sidebar from "../../components/app/Layout/Sidebar.vue";
import Topbar from "../../components/app/Layout/Topbar.vue";

export default {
  name: "InternalApp",
  components: {Topbar, Sidebar, DarkMode, RoleOnlyView, AppLogo},
  data() {
    return {
      is_update_available: false,
      has_default_tax: false,
      origin: '',
      has_api_key: true,
    }
  },
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
    dimissStripeImport: function() {
      axios.post('/app/settings/stripe-import/dismiss').then(response => {
        this.stripeImport();
      })
    },
    dimissUpdateNotification: function() {
      axios.post('/app/settings/update/dismiss').then(response => {
        this.is_update_available = false;
      })
    },
    ...mapActions('onboardingStore', ['setStripeImport', 'stripeImport', 'fetchData']),
  },
  mounted() {
    this.origin = window.location.hostname;
    this.fetchData()
  }
}
</script>

<style scoped>

</style>
