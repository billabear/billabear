<template>
  <li class="relative">
    <div class="p-3 grid grid-cols-4 hover:bg-teal-600 cursor-pointer"  @click="showSubmenu = !showSubmenu">
      <div class="col-span-3">

        <slot name="default"></slot>
      </div>
      <div class="text-end" v-if="hasSubmenu">
        <i class="fa-solid fa-chevron-left  transition duration-300" :class="{'-rotate-90': showSubmenu}"></i>
      </div>
    </div>
    <div class="overflow-hidden" v-if="showSubmenu">
      <transition
          enter-active-class="transition transform ease-out duration-300"
          enter-from-class="-translate-y-full opacity-0"
          enter-to-class="translate-y-0 opacity-100"
          leave-active-class="transition transform ease-in duration-300"
          leave-from-class="translate-y-0 opacity-100"
          leave-to-class="-translate-y-full opacity-0" v-if="hasSubmenu"
      >
        <div class="bg-teal-900">
          <ul>
            <slot name="submenu"></slot>
          </ul>
        </div>
      </transition>
    </div>
  </li>
</template>

<script setup>
import { ref, computed, useSlots } from 'vue'

// Component state
const showSubmenu = ref(false)

// Access slots in Composition API
const slots = useSlots()

// Computed property for checking if submenu slot exists
const hasSubmenu = computed(() => {
  return !!slots.submenu
})
</script>

<style scoped>

</style>
