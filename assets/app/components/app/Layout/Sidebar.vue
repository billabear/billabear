<template>
  <aside id="sidebar" class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 w-64 h-full pt-16 font-normal duration-75 lg:flex transition-width" aria-label="Sidebar">
    <div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
      <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
        <div class="flex-1 px-3 space-y-1 bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
          <ul class="pb-2 space-y-2">
            <li>
              <router-link :to="{name: 'app.home'}" class="sidebar-menu-item">
                <i class="fa-solid fa-chart-line"></i>
                <span class="ml-3" sidebar-toggle-item>Dashboard</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'app.customer.list'}" class="sidebar-menu-item">
                <i class="fa-solid fa-users"></i>
                <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.customers') }}</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'app.subscription'}" class="sidebar-menu-item">
                <i class="fa-solid fa-repeat"></i>
                <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.subscriptions') }}</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'app.invoices.list'}" class="sidebar-menu-item">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.invoices') }}</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'app.product.list'}" class="sidebar-menu-item">
                <i class="fa-solid fa-box"></i>
                <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.products') }}</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'app.payment.list'}" class="sidebar-menu-item">
                <i class="fa-solid fa-cash-register"></i>
                <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.transactions') }}</span>
              </router-link>
            </li>
            <li>
              <router-link :to="{name: 'app.reports'}" class="sidebar-menu-item">
                <i class="fa-solid fa-chart-simple"></i>
                <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.reports') }}</span>
              </router-link>
            </li>
            <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
              <li>
                <router-link :to="{name: 'app.settings'}" class="sidebar-menu-item">
                  <i class="fa-solid fa-gear"></i>
                  <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.settings') }}</span>
                </router-link>
              </li>
              <li>
                <router-link :to="{name: 'app.workflows'}" class="sidebar-menu-item">
                  <i class="fa-solid fa-route"></i>
                  <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.workflows') }}</span>
                </router-link>
              </li>
            </RoleOnlyView>
            <RoleOnlyView role="ROLE_DEVELOPER">
              <li>
                <router-link :to="{name: 'app.system.webhooks'}" class="sidebar-menu-item">
                  <i class="fa-solid fa-screwdriver-wrench"></i>
                  <span class="ml-3" sidebar-toggle-item>{{ $t('app.menu.main.system') }}</span>
                </router-link>
              </li>
            </RoleOnlyView>
          </ul>
          <div class="pt-2 space-y-2">
            <a  class="sidebar-menu-item" target="_blank" :href="'https://docs.billabear.com/user?utm_source=' + origin + '&utm_campaign=billabear_doc_links&utm_medium=update_announcement'">{{ $t('app.menu.main.docs') }} <i class="fa-solid fa-up-right-from-square"></i></a>
          </div>
        </div>
      </div>
      <div class="absolute bottom-0 left-0 justify-center hidden w-full p-4 space-x-4 bg-white lg:flex dark:bg-gray-800" sidebar-bottom-menu>
        <a href="#" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"></path></svg>
        </a>
        <a href="" data-tooltip-target="tooltip-settings" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path></svg>
        </a>
        <div id="tooltip-settings" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
          Settings page
          <div class="tooltip-arrow" data-popper-arrow></div>
        </div>
        <button type="button" data-dropdown-toggle="language-dropdown" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">
          <svg class="h-5 w-5 rounded-full mt-0.5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 3900 3900"><path fill="#b22234" d="M0 0h7410v3900H0z"/><path d="M0 450h7410m0 600H0m0 600h7410m0 600H0m0 600h7410m0 600H0" stroke="#fff" stroke-width="300"/><path fill="#3c3b6e" d="M0 0h2964v2100H0z"/><g fill="#fff"><g id="d"><g id="c"><g id="e"><g id="b"><path id="a" d="M247 90l70.534 217.082-184.66-134.164h228.253L176.466 307.082z"/><use xlink:href="#a" y="420"/><use xlink:href="#a" y="840"/><use xlink:href="#a" y="1260"/></g><use xlink:href="#a" y="1680"/></g><use xlink:href="#b" x="247" y="210"/></g><use xlink:href="#c" x="494"/></g><use xlink:href="#d" x="988"/><use xlink:href="#c" x="1976"/><use xlink:href="#e" x="2470"/></g></svg>
        </button>
        <!-- Dropdown -->
      </div>
    </div>
  </aside>

</template>

<script>
import RoleOnlyView from "../RoleOnlyView.vue";

export default {
  name: "Sidebar",
  components: {RoleOnlyView},
  mounted() {
    const sidebar = document.getElementById('sidebar');

    if (sidebar) {
      const toggleSidebarMobile = (sidebar, sidebarBackdrop, toggleSidebarMobileHamburger, toggleSidebarMobileClose) => {
        sidebar.classList.toggle('hidden');
        sidebarBackdrop.classList.toggle('hidden');
        toggleSidebarMobileHamburger.classList.toggle('hidden');
        toggleSidebarMobileClose.classList.toggle('hidden');
      }

      const toggleSidebarMobileEl = document.getElementById('toggleSidebarMobile');
      const sidebarBackdrop = document.getElementById('sidebarBackdrop');
      const toggleSidebarMobileHamburger = document.getElementById('toggleSidebarMobileHamburger');
      const toggleSidebarMobileClose = document.getElementById('toggleSidebarMobileClose');


      toggleSidebarMobileEl.addEventListener('click', () => {
        toggleSidebarMobile(sidebar, sidebarBackdrop, toggleSidebarMobileHamburger, toggleSidebarMobileClose);
      });

     /* sidebarBackdrop.addEventListener('click', () => {
        toggleSidebarMobile(sidebar, sidebarBackdrop, toggleSidebarMobileHamburger, toggleSidebarMobileClose);
      }); */
    }
  }
}
</script>

<style scoped>

</style>