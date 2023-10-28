<template>
  <div>
    <h1 class="page-title">{{ $t('app.reports.financial.lifetime.title') }}</h1>


    <LoadingScreen :ready="ready">
      <div class="card-body">
        <dl class="detail-list">
          <div>
            <dt>{{ $t('app.reports.financial.lifetime.lifespan') }}</dt>
            <dd>{{ $t('app.reports.financial.lifetime.lifespan_value', {lifespan: stats.lifespan}) }}</dd>
          </div>
          <div>
            <dt>{{ $t('app.reports.financial.lifetime.lifetime') }}</dt>
            <dd><Currency :amount="stats.lifetime_value" :currency="stats.currency" /></dd>
          </div>
        </dl>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import Currency from "../../../../components/app/Currency.vue";

export default {
  name: "LifetimeReport",
  components: {Currency},
  data() {
    return {
      ready: false,
      stats: {},
    }
  },
  mounted() {
    axios.get("/app/stats/lifetime").then(response => {
      this.stats = response.data;
      this.ready = true;

    })
  }
}
</script>

<style scoped>

</style>