<template>
  <div>
    <h1 class="page-title mb-5">{{ $t('app.reports.vat.overview.title') }}</h1>

    <LoadingScreen :ready="ready">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.reports.vat.overview.list.amount') }}</th>
            <th>{{ $t('app.reports.vat.overview.list.currency')}}</th>
            <th>{{ $t('app.reports.vat.overview.list.country') }}</th>
          </tr>
          </thead>
          <tbody v-if="loaded">
            <tr v-for="record in records">
              <Td>{{ displayCurrency(record.totalVat) }}</Td>
              <td>{{ record.currency }}</td>
              <td>{{ record.countryCode }}</td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="3"><LoadingMessage /></td>
            </tr>
          </tbody>
        </table>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";

export default {
  name: "VatOverview",
  data() {
    return {
      ready: false,
      records: [],
      loaded: true,
    }
  },
  mounted() {
    const date = new Date();
    axios.get('/app/reports/vat?when='+date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate()).then(
        response => {
          this.records = response.data.vat;
          this.ready = true;
        }
    )
  },
  methods: {

    displayCurrency: function (value) {
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
  }
}
</script>

<style scoped>

</style>