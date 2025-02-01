<template>
  <MenuContainer>
    <MenuTopItem>
      <template v-slot:default>
        <router-link :to="{name: 'app.home'}">
          <i class="fa-solid fa-house"></i>
          <span class="ml-3">{{ $t('app.menu.main.home') }}</span>
        </router-link>
      </template>
    </MenuTopItem>
    <MenuTopItem>
      <template v-slot:default>
          <i class="fa-solid fa-users"></i>
          <span class="ml-3">{{ $t('app.menu.main.customers') }}</span>
      </template>
      <template v-slot:submenu>
        <MenuSubItem>
          <router-link :to="{name: 'app.customer.list'}">{{ $t('app.menu.main.customer_list') }}</router-link>
        </MenuSubItem>
      </template>
    </MenuTopItem>
    <MenuTopItem>
      <template v-slot:default>
          <i class="fa-solid fa-repeat"></i>
          <span class="ml-3">{{ $t('app.menu.main.subscriptions') }}</span>
      </template>
      <template v-slot:submenu>
        <MenuSubItem>
          <router-link :to="{name: 'app.subscription.list'}">{{ $t('app.subscription.menu.subscriptions_list') }}</router-link>
        </MenuSubItem>
        <RoleOnlyView role="ROLE_USER">
          <MenuSubItem>
            <li class="submenu-list-item">
              <router-link :to="{name: 'app.subscription.mass_change.list'}">
                {{ $t('app.subscription.menu.mass_change') }}
              </router-link>
            </li>
          </MenuSubItem>
        </RoleOnlyView>
      </template>
    </MenuTopItem>
    <MenuTopItem>
      <template v-slot:default>
          <i class="fa-solid fa-file-invoice-dollar"></i>
          <span class="ml-3">{{ $t('app.menu.main.invoices') }}</span>
      </template>
      <template v-slot:submenu>
        <MenuSubItem>
          <router-link :to="{name: 'app.invoices.list'}">{{ $t('app.invoices.menu.invoices_list') }}</router-link>
        </MenuSubItem>
        <MenuSubItem>
          <router-link :to="{name: 'app.invoices.unpaid_list'}">{{ $t('app.invoices.menu.unpaid_invoices') }}</router-link>
        </MenuSubItem>
        <MenuSubItem>
          <router-link :to="{name: 'app.quotes.list'}">{{ $t('app.invoices.menu.quotes') }}</router-link>
        </MenuSubItem>
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <MenuSubItem>
            <router-link :to="{name: 'app.invoices.create'}">{{ $t('app.invoices.menu.create') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.invoice.settings'}">{{ $t('app.invoices.menu.settings') }}</router-link>
          </MenuSubItem>
        </RoleOnlyView>
      </template>
    </MenuTopItem>
    <RoleOnlyView role="ROLE_USER">
      <MenuTopItem>
        <template v-slot:default>
            <i class="fa-solid fa-box"></i>
            <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.products') }}</span>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.product.list'}">{{ $t('app.product.menu.products_list') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.metric.list'}">{{ $t('app.product.menu.metrics') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.feature.list'}">{{ $t('app.product.menu.features') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.vouchers.list'}">{{ $t('app.product.menu.vouchers') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>
      <MenuTopItem>
        <template v-slot:default>
          <i class="fa-solid fa-chart-simple"></i>
          <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.reports') }}</span>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.report.subscriptions'}" class="submenu-link">{{ $t('app.reports.menu.subscriptions') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.report.churn'}" class="submenu-link">{{ $t('app.reports.menu.churn') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.report.lifetime'}" class="submenu-link">{{ $t('app.reports.menu.lifetime') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>
      <MenuTopItem>
        <template v-slot:default>
            <i class="fa-solid fa-cash-register"></i>
            <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.finance') }}</span>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.payment.list'}">{{ $t('app.transactions.menu.payments') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.refund.list'}">{{ $t('app.transactions.menu.refunds') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.charge_backs.list'}">{{ $t('app.transactions.menu.charge_backs') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.checkout.list'}">{{ $t('app.transactions.menu.checkout') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>
    </RoleOnlyView>
    <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
      <MenuTopItem>
        <template v-slot:default>
            <i class="fa-solid fa-gear"></i>
            <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.settings') }}</span>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.pdf_template.list'}" class="submenu-link">{{ $t('app.settings.menu.pdf_templates') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.email_template.list'}" class="submenu-link">{{ $t('app.settings.menu.email_templates') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.brand_settings.list'}" class="submenu-link">{{ $t('app.settings.menu.brand_settings') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.exchange_rates.list'}" class="submenu-link">{{ $t('app.settings.menu.exchange_rates') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.import.stripe'}" class="submenu-link">{{ $t('app.settings.menu.stripe') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.users.list'}" class="submenu-link">{{ $t('app.settings.menu.users') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.notification_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.notification_settings') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.system_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.system_settings') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>

      <MenuTopItem>
        <template v-slot:default>
          <i class="fa-solid fa-money-bill"></i>
          <span class="ml-3">{{ $t('app.menu.main.tax') }}</span>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.finance.country.list'}">{{ $t('app.transactions.menu.countries') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.finance.tax_type.list'}">{{ $t('app.transactions.menu.tax_types') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.report.tax'}" class="submenu-link">{{ $t('app.reports.menu.tax') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.tax_settings.update'}" class="submenu-link">{{ $t('app.settings.menu.tax_settings') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.tax_settings.vatsense'}" class="submenu-link">{{ $t('app.settings.menu.vat_sense') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>

      <MenuTopItem>
        <template v-slot:default>
            <i class="fa-solid fa-route"></i>
            <span class="ml-3">{{ $t('app.menu.main.workflows') }}</span>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.workflows.cancellation_request.list'}" class="submenu-link">{{ $t('app.workflows.menu.cancellation_requests') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.workflows.subscription_creation.list'}" class="submenu-link">{{ $t('app.workflows.menu.subscription_creation') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.workflows.payment_creation.list'}" class="submenu-link">{{ $t('app.workflows.menu.payment_creation') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.workflows.charge_back_creation.list'}" class="submenu-link">{{ $t('app.workflows.menu.charge_back_creation') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.workflows.refund_created_process.list'}" class="submenu-link">{{ $t('app.workflows.menu.refund_created_process') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.workflows.payment_failure_process.list'}" class="submenu-link">{{ $t('app.workflows.menu.payment_failure_process') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>
      <MenuTopItem>
        <template v-slot:default>
          <i class="fa-solid fa-code-compare"></i>
          <span class="ml-3" sidebar-toggle-item>{{ $t('app.integrations.menu.main') }}</span>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.integrations.list'}" class="submenu-link">{{ $t('app.integrations.menu.notifications') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.integrations.accounting'}">{{ $t('app.integrations.menu.accounting') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.integrations.customer_support'}">{{ $t('app.integrations.menu.customer_support') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.integrations.newsletter'}">{{ $t('app.integrations.menu.newsletter') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.integrations.crm'}">{{ $t('app.integrations.menu.crm') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>
    </RoleOnlyView>
    <RoleOnlyView role="ROLE_DEVELOPER">
      <MenuTopItem>
        <template v-slot:default>
          <router-link :to="{name: 'app.system.webhooks'}">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <span class="ml-3">{{ $t('app.menu.main.developers') }}</span>
          </router-link>
        </template>
        <template v-slot:submenu>
          <MenuSubItem>
            <router-link :to="{name: 'app.system.webhooks'}">{{ $t('app.system.menu.webhooks') }}</router-link>
          </MenuSubItem>
          <MenuSubItem>
            <router-link :to="{name: 'app.settings.api_keys.main'}">{{ $t('app.settings.menu.api_keys') }}</router-link>
          </MenuSubItem>
        </template>
      </MenuTopItem>
    </RoleOnlyView>

    <MenuTopItem>
      <a  class="sidebar-menu-item" target="_blank" :href="'https://docs.billabear.com/user?utm_source=' + origin + '&utm_campaign=billabear_doc_links&utm_medium=update_announcement'">{{ $t('app.menu.main.docs') }} <i class="fa-solid fa-up-right-from-square"></i></a>
    </MenuTopItem>
  </MenuContainer>
</template>

<script>
import MenuSubItem from "./MenuSubItem.vue";
import MenuTopItem from "./MenuTopItem.vue";
import MenuContainer from "./MenuContainer.vue";
import RoleOnlyView from "../RoleOnlyView.vue";

export default {
  name: "MenuDesktop",
  components: {RoleOnlyView, MenuContainer, MenuTopItem, MenuSubItem},
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
