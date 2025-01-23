<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.exchange_rates.title') }}</h1>
    <LoadingScreen :ready="loaded">

      <div class="rounded-lg bg-white shadow p-3">
        <table class="w-full">
          <thead>
          <tr class="border-b border-black">
            <th class="text-left pb-2">{{ $t('app.settings.exchange_rates.list.currency_code') }}</th>
            <th class="text-left pb-2">{{ $t('app.settings.exchange_rates.list.rate') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="rate in rates" class="mt-5">
            <td class="py-3">{{ rate.currency_code }}</td>
            <td>{{ rate.rate }}</td>
          </tr>
          <tr v-if="rates.length === 0">
            <td colspan="2" class="py-3 text-center">{{ $t('app.settings.exchange_rates.list.no_rates') }}</td>
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
