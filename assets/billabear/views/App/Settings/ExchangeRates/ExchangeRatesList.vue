<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.exchange_rates.title') }}</h1>
    <LoadingScreen :ready="loaded">

      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.settings.exchange_rates.list.currency_code') }}</th>
            <th>{{ $t('app.settings.exchange_rates.list.rate') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="rate in rates" class="mt-5">
            <td>{{ rate.currency_code }}</td>
            <td>{{ rate.rate }}</td>
          </tr>
          <tr v-if="rates.length === 0">
            <td colspan="2" class="text-center">{{ $t('app.settings.exchange_rates.list.no_rates') }}</td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-5">
        <a href="https://www.exchangerate-api.com" target="_blank">Rates By Exchange Rate API</a>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ExchangeRatesList",
  mounted() {
    axios.get('/app/exchange-rates').then(response => {
      this.rates = response.data.rates;
      this.loaded = true;
    })
  },
  data() {
    return {
      rates: [],
      loaded: true
    }
  }
}
</script>

<style scoped>

</style>