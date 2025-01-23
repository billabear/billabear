<template>
  <div class="">
    <div class="p-5 text-center" @click="show = !show" v-if="!show">
      {{ $t('app.menu.main.mobile.show') }}
    </div>
    <div class="p-5 text-center" @click="show = !show" v-else>
      {{ $t('app.menu.main.mobile.hide') }}
    </div>
    <MenuDesktop v-if="show" />
  </div>
</template>

<script>
import MenuDesktop from "./MenuDesktop.vue";

export default {
  name: "MobileMenu",
  components: {MenuDesktop},
  data() {
    return {
      show: false,
    }
  }
}
</script>

<style scoped>

</style>
