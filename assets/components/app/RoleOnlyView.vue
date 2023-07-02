<template>
  <slot v-if="hasRole"></slot>
</template>

<script>
export default {
  name: "RoleOnlyView",
  props: {
    role: {
      type: String,
      default() {
        return "ROLE_USER";
      }
    },
  },
  computed: {
    hasRole: function () {
      var data;
      try {
        data = JSON.parse(localStorage.getItem('user'))
      } catch (e) {
        return false;
      }

      if (this.$props.role === 'ROLE_USER') {
        return true;
      }

      if (data.roles.includes('ROLE_ADMIN')) {
        return true;
      }

      if (data.roles.includes('ROLE_DEVELOPER') && (this.$props.role !== 'ROLE_ADMIN')) {
        return true;
      }

      if (data.roles.includes('ROLE_ACCOUNT_MANAGER')  && (this.$props.role !== 'ROLE_ADMIN' && this.$props.role !== 'ROLE_DEVELOPER')) {
        return true;
      }
      if (data.roles.includes('ROLE_CUSTOMER_SUPPORT') && (this.$props.role !== 'ROLE_ADMIN' && this.$props.role !== 'ROLE_DEVELOPER' && this.$props.role !== 'ROLE_ACCOUNT_MANAGER')) {
        return true;
      }

      if (data.roles === ['ROLE_USER'] && this.$props.role === 'ROLE_USER') {
        return true;
      }

      return false;
    }
  }
}
</script>

<style scoped>

</style>