<template>
  <slot v-if="hasRole"></slot>
</template>

<script setup>
import { computed } from 'vue'

// Define component props
const props = defineProps({
  role: {
    type: String,
    default: 'ROLE_USER'
  }
})

// Computed property for checking if user has required role
const hasRole = computed(() => {
  let data;
  try {
    data = JSON.parse(localStorage.getItem('user'))
  } catch (e) {
    return false;
  }

  if (props.role === 'ROLE_CUSTOMER_SUPPORT') {
    return true;
  }

  if (data.roles.includes('ROLE_ADMIN')) {
    return true;
  }

  if (data.roles.includes('ROLE_DEVELOPER') && (props.role !== 'ROLE_ADMIN')) {
    return true;
  }

  if (data.roles.includes('ROLE_ACCOUNT_MANAGER') && (props.role !== 'ROLE_ADMIN' && props.role !== 'ROLE_DEVELOPER')) {
    return true;
  }

  if (data.roles.includes('ROLE_USER') && (props.role !== 'ROLE_ADMIN' && props.role !== 'ROLE_DEVELOPER' && props.role !== 'ROLE_ACCOUNT_MANAGER')) {
    return true;
  }

  if (data.roles == ['ROLE_CUSTOMER_SUPPORT'] && props.role === 'ROLE_CUSTOMER_SUPPORT') {
    return true;
  }

  return false;
})
</script>

<style scoped>

</style>
