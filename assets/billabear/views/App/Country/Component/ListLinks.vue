<script>
export default {
  name: "ListLinks",
  props: {
    total: {
      type: Number,
    },
    registrationRequired: {
      type: Number,
    },
    collecting: {
      type: Number,
    },
  }
}
</script>

<template>
  <div class="grid grid-cols-3 gap-5 my-3">
    <router-link :to="{name: 'app.finance.country.collecting'}" class="border bg-white rounded-lg shadow p-3 text-3xl text-black no-underline">
      {{ $t('app.country.list.links.collecting') }}
      <br /><span class="text-5xl pt-3 block">{{ collecting }}</span>
    </router-link>
    <router-link :to="{name: 'app.finance.country.registration_required'}" class="border bg-white rounded-lg shadow p-3 text-3xl text-black no-underline">
      {{ $t('app.country.list.links.registration_required') }}
      <br /><span class="text-5xl pt-3 block">{{ registrationRequired }}</span>
    </router-link>
    <router-link :to="{name: 'app.finance.country.list'}" class="border bg-white rounded-lg shadow p-3 text-3xl text-black no-underline hover:bg-gray-100">
      {{ $t('app.country.list.links.total') }}
      <br /><span class="text-5xl pt-3 block">{{ total }}</span>
    </router-link>
  </div>
</template>

<style scoped>

</style>