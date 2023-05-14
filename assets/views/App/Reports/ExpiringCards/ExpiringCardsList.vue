<template>
  <div>
    <h1 class="page-title">{{ $t('app.reports.expiring_cards.main.title') }}</h1>
    <LoadingScreen :ready="ready">
      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.reports.expiring_cards.main.list.customer_email') }}</th>
            <th>{{ $t('app.reports.expiring_cards.main.list.card_number')}}</th>
            <th></th>
          </tr>
          </thead>
          <tbody v-if="loaded">
          <tr v-for="card in expiringCards" class="mt-5 cursor-pointer">
            <td>{{ card.customer.email }}</td>
            <td>**** **** **** {{ card.payment_card.last_four }}</td>
            <td><router-link :to="{name: 'app.customer.view', params: {id: card.customer.id}}" class="list-btn">{{ $t('app.reports.expiring_cards.main.list.view') }}</router-link></td>

          </tr>
          <tr v-if="expiringCards.length === 0">
            <td colspan="4" class="text-center">{{ $t('app.reports.expiring_cards.main.list.no_expiring_cards') }}</td>
          </tr>
          </tbody>
          <tbody v-else>
          <tr>
            <td colspan="4" class="text-center">
              <LoadingMessage>{{ $t('app.report.expiring_cards.main.list.loading') }}</LoadingMessage>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "ExpiringCardsList",
  data() {
    return {
      loaded: true,
      ready: false,
      expiringCards: [],
    }
  },
  mounted() {
    axios.get("/app/reports/expiring-cards").then(response => {
      this.ready = true;
      this.expiringCards = response.data.data;
    })
  }
}
</script>

<style scoped>

</style>