<template>
  <div class="overflow-hidden">
    <h1 class="page-title">{{ $t('app.reports.tax.title') }}</h1>

    <LoadingScreen :ready="ready">

      <div class="mt-5 grid grid-cols-4 gap-5">
        <div class="col-span-3 map-body">
          <h2 class="report-subtitle">{{ $t('app.reports.tax.map.title') }}</h2>
          <WorldMap :dataset="mapData" />
        </div>

        <div class="country-body">

          <h2 class="report-subtitle">{{ $t('app.reports.tax.countries.title') }}</h2>

          <div class="my-3 border p-2 hover:bg-gray-100" v-for="country in rawCountryData">
            <div class="country-title">{{ country.country.name }}</div>
            <div class="country-data" v-html="$t('app.reports.tax.countries.transacted_amount', {transacted_amount: displayCurrency(country.transacted_amount), currency: Number().toLocaleString(undefined, {style:'currency', currency:country.country.currency}).slice(0,1)  })"></div>
            <div class="country-data" v-html="$t('app.reports.tax.countries.collected_amount', {collected_amount: displayCurrency(country.collected_amount), currency: Number().toLocaleString(undefined, {style:'currency', currency:country.country.currency}).slice(0,1)  })"></div>
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

      <div class="grid grid-cols-2">
        <div><h3 class="text-2xl font-bold my-5">{{ $t('app.reports.tax.transactions.title') }}</h3></div>
        <div class="text-end my-5"><button @click="processExport" class="btn--main">
          <i class="fa-solid fa-download"></i> {{ $t('app.reports.tax.transactions.download') }}</button>
        </div>
      </div>
      <div class="overflow-hidden rounded-xl border border-gray-300 bg-white p-5" >
        <div class="overflow-auto">
          <table>
            <thead>
            <tr>
              <th v-for="column in columns">{{ column}}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="row in rawTransactionData">
              <td v-for="column in columns">{{ row[column] }}</td>
            </tr>
            </tbody>
          </table>
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
import {Button} from "flowbite-vue";

export default {
  name: "TaxReportDashboard",
  components: {Button, ProgressBar, WorldMap},
  data() {
    return {
      ready: false,
      records: [],
      loaded: true,
      mapData: {},
      rawCountryData: null,
      rawTransactionData: [],
    }
  },
  mounted() {
    axios.get("/app/tax/report").then(response => {
      this.rawCountryData = response.data.active_countries;
      this.rawTransactionData = response.data.latest_tax_items;
      this.mapData = this.rawCountryData.map(obj => {
        return {code: obj.country.iso_code_3, value: obj.collected_amount, formatted_value: this.displayCurrency(obj.collected_amount), label: obj.country.currency }
      }).filter(obj => {
        return (ob.value > 0);
      });

      this.ready = true;
    })
  },
  computed: {
    columns: function () {
      if (this.rawTransactionData.length == 0) {
        return ['total'];
      }
      return Object.keys(this.rawTransactionData[0]);
    }
  },
  methods: {
    displayCurrency: function (value) {
      if (!value){
        return '0';
      }
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
    processExport: function () {
      this.exportInProgress = true;
      var subscriptionId = this.$route.params.id
      axios.get('/app/tax/report/export', { responseType: 'blob' })
          .then((response) => {
            const contentType = response.headers['content-type'];

            if (contentType.includes('application/json')) {
              // Response is JSON
              return response.data.text();
            } else {
              const contentDisposition = response.headers['content-disposition'];
              var fileDownload = require('js-file-download');
              const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDisposition);
              if (matches !== null && matches[1]) {
                const fileName = matches[1].replace(/['"]/g, '');
                fileDownload(response.data, fileName);
                this.exportInProgress = false;
              }
            }
          })
          .then((responseData) => {
            if (responseData) {
              if (typeof responseData === 'string') {
                const jsonData = JSON.parse(responseData);
                console.log('Response is JSON:', jsonData);
              }
            }
          })
          .catch((error) => {
            console.error('An error occurred:', error);
          });
    }
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
