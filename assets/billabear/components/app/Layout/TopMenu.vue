<template>

  <Menu as="div" class="relative inline-block text-end">
    <div>
      <MenuButton
          class="inline-flex w-full justify-center text-gray-600 px-4 py-2 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
      >
        {{ user.username }}
        <i class="fa-solid fa-chevron-down cursor-pointer transition duration-300 pt-1 ml-2"></i>

      </MenuButton>
    </div>

    <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems
          class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
      >
        <div class="px-1 py-1">
          <MenuItem v-slot="{ active }">
            <router-link :to="{name: 'app.user.settings'}"

                         :class="[
                  active ? 'bg-teal-500 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
            >{{ $t('app.layout.topbar.menu.settings')}}</router-link>

          </MenuItem>
          <MenuItem v-slot="{ active }">
            <a  href="/app/logout"
                :class="[
                  active ? 'bg-teal-500 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
            >
              {{ $t('app.layout.topbar.menu.signout')}}
            </a>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script>
import {Menu, MenuButton, MenuItems, MenuItem} from "@headlessui/vue";
import {mapState} from "vuex";

export default {
  name: "TopMenu",
  components: {Menu, MenuItems, MenuButton, MenuItem},
  computed: {
    ...mapState('userStore', ['user'])
  }
}
</script>

<style scoped>

</style>
