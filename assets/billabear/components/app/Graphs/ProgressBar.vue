<template>
  <div class="progress-bar-container">
    <div class="progress-bar">
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  total: {
    type: Number,
    required: true,
  },
  current: {
    type: Number,
    required: true,
  }
});

const percentage = computed(() => {
  if (props.current > props.total) {
    return '100%';
  }
  return (props.current / props.total * 100) + '%';
});
</script>

<style scoped>
.progress-bar-container {
  @apply w-full border-gray-300 border h-2 bg-white;
}
.progress-bar {
  width: v-bind('percentage');
  @apply bg-amber-400 h-2;
}
</style>
