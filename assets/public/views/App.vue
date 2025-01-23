<template>
  <div class="w-screen h-screen">
    <router-view></router-view>
    <ModalsContainer />
  </div>
</template>

<script>
export default {
  name: "App"
}
</script>

<style>
#app {
  height: 100%;
}
</style>
