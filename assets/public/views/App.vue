<template>
  <div class="w-full mx-auto lg:w-1/2 bg-white h-screen p-5 shadow-main">
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
body {
  background: #F19B1BFF;
}
#app {
  height: 100%;
}
.shadow-main {
  box-shadow: rgba(0, 0, 0, 0.75) 0px -1px 40px 3px; background-color: rgb(231, 166, 26);
}
</style>