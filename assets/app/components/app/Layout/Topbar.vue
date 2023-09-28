<template>

  <nav class="fixed z-30 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-start">
          <button id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar" class="p-2 text-gray-600 rounded cursor-pointer lg:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
            <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
            <svg id="toggleSidebarMobileClose" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
          </button>
          <AppLogo />

        </div>
        <div class="flex items-center">

          <DarkMode />
          <Dropdown placement="left">
            <template #trigger>
              <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" id="user-menu-button-2" aria-expanded="false" data-dropdown-toggle="dropdown-2">

                <span class="sr-only">Open user menu</span>
                <img class="w-8 h-8 bg-white rounded-full" src="/images/user-avatar.svg" alt="user photo">
              </button>
            </template>
            <template #default>
              <ul class="py-1" role="none">
                <li>
                  <router-link :to="{name: 'app.user.settings'}"  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">{{ $t('app.layout.topbar.menu.settings')}}</router-link>
                </li>
                <li>
                  <a href="/app/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">{{ $t('app.layout.topbar.menu.signout')}}</a>
                </li>
              </ul>
            </template>
          </Dropdown>

          <!-- Dropdown menu -->
        </div>
      </div>
    </div>
  </nav>

</template>

<script>
import DarkMode from "../DarkMode.vue";
import RoleOnlyView from "../RoleOnlyView.vue";
import AppLogo from "../AppLogo.vue";
import {Dropdown, ListGroupItem, ListGroup} from "flowbite-vue";

export default {
  name: "Topbar",

  components: {ListGroupItem, DarkMode, RoleOnlyView, AppLogo, Dropdown},
}
</script>

<style scoped>

</style>