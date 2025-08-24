<template>
  <div class="sm:grid sm:grid-cols-2 m-2">
    <div class="mt-4">
      <button 
        @click="$emit('prev-page')" 
        v-if="showBack" 
        class="btn--main mr-3"
        :disabled="loading"
      >
        {{ $t('app.customer.list.prev') }}
      </button>
      <button 
        @click="$emit('next-page')" 
        v-if="hasMore" 
        class="btn--main"
        :disabled="loading"
      >
        {{ $t('app.customer.list.next') }}
      </button>
    </div>
    <div class="mt-4 text-end">
      <select 
        class="rounded-lg border border-gray-300" 
        @change="$emit('change-per-page', $event.target.value)" 
        :value="perPage"
        :disabled="loading"
      >
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
  </div>
</template>

<script>
export default {
  name: "CustomerPagination",
  props: {
    showBack: {
      type: Boolean,
      required: true,
      default: false
    },
    hasMore: {
      type: Boolean,
      required: true,
      default: false
    },
    perPage: {
      type: [String, Number],
      required: true,
      default: "10"
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['prev-page', 'next-page', 'change-per-page']
}
</script>