<template>
  <div>
    <h1 class="page-title">{{ $t('app.reports.tax.title') }}</h1>

    <LoadingScreen :ready="ready">

      <div class="mt-5 grid grid-cols-4 gap-5">
        <div class="col-span-3 map-body">
          <h2 class="report-subtitle">{{ $t('app.reports.tax.map.title') }}</h2>
          <WorldMap :dataset="mapData" />
        </div>

        <div class="country-body">

          <h2 class="report-subtitle">{{ $t('app.reports.tax.countries.title') }}</h2>

          <div class="my-3 border p-2" v-for="country in rawCountryData">
            <div class="country-title">{{ country.country.name }}</div>
            <div class="country-data" v-html="$t('app.reports.tax.countries.transacted_amount', {transacted_amount: displayCurrency(country.transacted_amount), currency: Number().toLocaleString(undefined, {style:'currency', currency:country.country.currency}).slice(0,1)  })"></div>
            <div class="country-data" v-html="$t(
                'app.reports.tax.countries.threshold_status',
                {status: country.threshold_reached ?
                $t('app.reports.tax.countries.threshold_reached') :
                $t('app.reports.tax.countries.threshold_not_reached'),
                })"> </div>

            <ProgressBar :current="country.transacted_amount" :total="country.threshold_amount" />
          </div>
        </div>
      </div>
    </LoadingScreen>
  </div>

</template>

<script>

import WorldMap from "../../../../components/app/Graphs/WorldMap.vue";
import axios from "axios";
import currency from "currency.js";
import ProgressBar from "../../../../components/app/Graphs/ProgressBar.vue";

export default {
  name: "TaxReportDashboard",
  components: {ProgressBar, WorldMap},
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
  @apply rounded-xl border border-gray-300 bg-white p-5;
  max-height: 600px
}
.report-subtitle {
  @apply font-bold;
}

.country-body {
  @apply rounded-xl bg-white border border-gray-300 p-5 overflow-y-auto;
  max-height: 600px
}
.country-title {
  @apply font-bold text-sm pb-1;
}
.country-data {
  @apply text-xs;
}

</style>
