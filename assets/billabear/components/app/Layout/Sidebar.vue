<template>

</template>

<script>
import RoleOnlyView from "../RoleOnlyView.vue";
import {Select} from "flowbite-vue";
import {mapActions, mapState} from "vuex";

export default {
  name: "Sidebar",
  components: {Select, RoleOnlyView},
  data() {
    return {
      origin: '',
    }
  },
  computed: {
    ...mapState('userStore', ['locale']),
  },
  methods: {
    ...mapActions('userStore', ['updateLocale']),
    changeLocale: function(value) {
      this.updateLocale({locale: value.target.value})
      this.$i18n.locale = this.locale;
    },
    updateValue(event) {
      this.localSelectedValue = event.target.value;
    },
  },
  mounted() {
    const sidebar = document.getElementById('sidebar');

    this.origin = window.location.hostname;

  }
}
</script>

<style scoped>

</style>
