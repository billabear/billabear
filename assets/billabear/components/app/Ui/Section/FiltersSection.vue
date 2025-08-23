<template>

  <div class="mb-3 md:w-72 rounded-lg bg-white shadow p-3">
    <form @submit.prevent="doSearch">

    <div class="divide-y space-y-2">
      <h3 class="text-xl">{{ $t('app.customer.list.filter.title') }}</h3>
      <div v-for="(filter, filterKey) in filters">
        <div class="pt-1">
          {{ $t(''+filter.label+'') }}
        </div>
        <div v-if="filter.type == 'text'">
          <InputText class="w-full" v-model="filter.value" />
        </div>
        <div v-else-if="filter.type === 'choice'">
          <select class="w-full form-field-input" v-model="filter.value">
            <option></option>
            <option v-for="option in filter.choices" :key="option.value" :value="option.value">
              {{ $t(''+option.label+'') }}
            </option>
          </select>
        </div>
        <div v-else-if="filter.type === 'boolean'">
          <Toggle v-model="filter.value" />
        </div>
      </div>
      <div v-if="Object.keys(filters).length === 0">
        <div class="pt-1">
          {{ $t('app.customer.list.filter.no_filters') }}
        </div>
      </div>
    </div>
    <div class="mt-5">
      <button @click="doSearch" type="submit" class="btn--main w-full">{{ $t('app.customer.list.filter.search') }}</button>
    </div>

    </form>
  </div>
</template>

<script>
import InputText from "../Forms/InputText.vue";
import {Toggle} from "flowbite-vue";

export default {
  name: "FiltersSection",
  components: {Toggle, InputText},
  props: {
    filters: Array,
  },
  methods: {

    doSearch: function () {
      const queryVals = this.buildFilterQuery();
      this.$router.push({query: queryVals})
    },
    buildFilterQuery: function () {
      const queryVals = {};
      for (let filter in this.filters) {
        if (this.filters[filter].value !== null && this.filters[filter].value !== undefined) {
          queryVals[filter] = this.filters[filter].value;
        }
      }

      if (this.$route.query.per_page !== undefined) {
        queryVals.per_page = this.$route.query.per_page;
        this.per_page=this.$route.query.per_page;
      }

      return queryVals;
    },
  }
}
</script>

<style scoped>

</style>
