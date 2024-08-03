<template>



</template>

<script>
import DarkMode from "../DarkMode.vue";
import RoleOnlyView from "../RoleOnlyView.vue";
import AppLogo from "../AppLogo.vue";
import {Dropdown, ListGroupItem} from "flowbite-vue";

export default {
  name: "Topbar",

  components: {ListGroupItem, DarkMode, RoleOnlyView, AppLogo, Dropdown},
}
</script>

<style scoped>

</style>
