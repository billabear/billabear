<template>
  <div class="progress-bar-container">
    <div class="progress-bar">
    </div>
  </div>
</template>

<script>
export default {
  name: "ProgressBar",
  props: {
    total: {
      type: Number,
      required: true,
    },
    current:  {
      type: Number,
      required: true,
    }
  },
  computed: {
    percentage: function () {
      if (this.current > this.total) {
        return '100%';
      }
      return (this.current / this.total * 100) + '%';
    }
  }
}
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
