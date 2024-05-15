<template>
  <div>
    <h1 class="page-title">{{ $t('app.reports.tax.title') }}</h1>

    <LoadingScreen :ready="ready">

      <div class="mt-5 grid grid-cols-4 gap-5">
        <div class="col-span-3 map-body">
          <WorldMap :dataset="mapData" />
        </div>
      </div>
    </LoadingScreen>
  </div>

</template>

<script>

import WorldMap from "../../../../components/app/Graphs/WorldMap.vue";
import axios from "axios";
import currency from "currency.js";

export default {
  name: "TaxReportDashboard",
  components: {WorldMap},
  data() {
    return {
      ready: false,
      records: [],
      loaded: true,
      mapData: {},
      rawCountryData: null,
    }
  },
  mounted() {
    axios.get("/app/tax/report").then(response => {
      this.rawCountryData = response.data.active_countries;
      this.mapData = this.rawCountryData.map(obj => {
        return {code: obj.country.iso_code_3, value: obj.transacted_amount, formatted_value: this.displayCurrency(obj.transacted_amount), label: obj.country.currency }
      });

      this.ready = true;
    })
  },
  methods: {

    displayCurrency: function (value) {
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
  }
}
</script>

<style scoped>
.map-body {
  @apply rounded border border-gray-300 bg-white p-5;

}
</style>
