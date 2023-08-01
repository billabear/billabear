<template>
  <div class="header">
    <div class="page-header">
      <div class="page-container">
        <div class="page-content">

          <AppLogo />
          <Menu>
            <MenuGroup>
              <MenuItem route-name="app.report.dashboard">{{ $t('app.menu.main.reports') }}</MenuItem>
              <MenuItem route-name="app.payment.list">{{ $t('app.menu.main.transactions') }}</MenuItem>
              <MenuItem route-name="app.product.list">{{ $t('app.menu.main.products') }}</MenuItem>
              <MenuItem route-name="app.customer.list">{{ $t('app.menu.main.customers') }}</MenuItem>
              <MenuItem route-name="app.subscription.list">{{ $t('app.menu.main.subscriptions') }}</MenuItem>
              <MenuItem route-name="app.invoices.list">{{ $t('app.menu.main.invoices') }}</MenuItem>
              <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
                <MenuItem route-name="app.user.settings">{{ $t('app.menu.main.settings') }}</MenuItem>
              </RoleOnlyView>
              <RoleOnlyView role="ROLE_DEVELOPER">
                <MenuItem route-name="app.system.webhooks">{{ $t('app.menu.main.system') }}</MenuItem>
              </RoleOnlyView>
              <li class="menu-item"><a class="menu-link" target="_blank" :href="'https://docs.billabear.com/user?utm_source=' + origin + '&utm_campaign=billabear_doc_links&utm_medium=update_announcement'">{{ $t('app.menu.main.docs') }} <i class="fa-solid fa-up-right-from-square"></i></a></li>
            </MenuGroup>
          </Menu>
        </div>
      </div>
    </div>
    <div class="mt-5">
      <div class="page-container">
        <div class="page-content">
          <RoleOnlyView role="ROLE_ADMIN">
            <div class="alert-error" v-if="!has_stripe_imports">
              {{ $t('app.home.stripe_import.text') }} <router-link :to="{name: 'app.settings.import.stripe'}">{{ $t('app.home.stripe_import.link') }}</router-link> - <a href="#" @click="dimissStripeImport">{{ $t('app.home.stripe_import.dismiss') }}</a>
            </div>
          </RoleOnlyView>
          <RoleOnlyView role="ROLE_DEVELOPER">
            <div class="alert-success" v-if="is_update_available">
              {{ $t('app.home.update_available.text') }} <a target="_blank" :href="'https://docs.billabear.com/technical/update?utm_source=' + origin + '&utm_campaign=billabear_doc_links&utm_medium=update_announcement'">{{ $t('app.home.update_available.link') }}</a> - <a  href="#"  @click="dimissUpdateNotification">{{ $t('app.home.update_available.dismiss') }}</a>
            </div>
          </RoleOnlyView>
          <div class="alert-error my-3" v-if="!has_default_tax">
            {{ $t('app.home.default_tax.text') }} <router-link :to="{name: 'app.settings.brand_settings.list'}">{{ $t('app.home.default_tax.link') }}</router-link>
          </div>
        <router-view></router-view>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import AppLogo from "../../components/app/AppLogo.vue";
import axios from "axios";
import {mapActions, mapState} from "vuex";
import RoleOnlyView from "../../components/app/RoleOnlyView.vue";
export default {
  name: "InternalApp",
  components: {RoleOnlyView, AppLogo},
  data() {
    return {
      is_update_available: false,
      has_default_tax: false,
      origin: '',
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
    dimissUpdateNotification: function() {
      axios.post('/app/settings/update/dismiss').then(response => {
        this.is_update_available = false;
      })
    },
    ...mapActions('onboardingStore', ['setStripeImport', 'stripeImport']),
  },
  mounted() {
    this.origin = window.location.hostname;
    axios.get("/app/system/data").then(response => {
      this.setStripeImport({defaultValue: response.data.has_stripe_import});
      this.is_update_available = response.data.is_update_available;
      this.has_default_tax = response.data.has_default_tax;
    })
  }
}
</script>

<style scoped>

</style>
