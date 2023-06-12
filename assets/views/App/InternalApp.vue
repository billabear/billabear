<template>
  <div class="header">
    <div class="page-header">
      <div class="page-container">
        <div class="page-content">

          <AppLogo />
          <Menu>
            <MenuGroup>
              <MenuItem route-name="app.report.dashboard">{{ $t('app.menu.main.reports') }}</MenuItem>
              <MenuItem route-name="app.transactions">{{ $t('app.menu.main.transactions') }}</MenuItem>
              <MenuItem route-name="app.product">{{ $t('app.menu.main.products') }}</MenuItem>
              <MenuItem route-name="app.customer.list">{{ $t('app.menu.main.customers') }}</MenuItem>
              <MenuItem route-name="app.subscription.list">{{ $t('app.menu.main.subscriptions') }}</MenuItem>
              <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
                <MenuItem route-name="app.user.settings">{{ $t('app.menu.main.settings') }}</MenuItem>
              </RoleOnlyView>
            </MenuGroup>
          </Menu>
        </div>
      </div>
    </div>
    <div class="mt-5">
      <div class="page-container">
        <div class="page-content">
      <div class="alert-error" v-if="!has_stripe_imports">
        {{ $t('app.home.stripe_import.text') }} <router-link :to="{name: 'app.settings.import.stripe'}">{{ $t('app.home.stripe_import.link') }}</router-link> - <a @click="dimissStripeImport">{{ $t('app.home.stripe_import.dismiss') }}</a>
      </div>
        <router-view></router-view>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import AppLogo from "../../components/app/AppLogo";
import axios from "axios";
import {mapActions, mapState} from "vuex";
import RoleOnlyView from "../../components/app/RoleOnlyView.vue";
export default {
  name: "InternalApp",
  components: {RoleOnlyView, AppLogo},
  data() {
    return {
    }
  },
  computed: {
    ...mapState('onboardingStore', ['has_stripe_imports'])
  },
  methods: {
    dimissStripeImport: function() {
        axios.post('/app/settings/stripe-import/dismiss').then(response => {
            this.stripeImport();
        })
    },
    ...mapActions('onboardingStore', ['setStripeImport', 'stripeImport']),
  },
  mounted() {
    axios.get("/app/system/data").then(response => {
      this.setStripeImport({defaultValue: response.data.has_stripe_import});
    })
  }
}
</script>

<style scoped>

</style>
