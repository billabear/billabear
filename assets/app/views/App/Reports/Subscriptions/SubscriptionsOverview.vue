<template>
  <div>
    <h1 class="page-title mb-5">{{ $t('app.reports.subscriptions.overview.title') }}</h1>
    <LoadingScreen :ready="ready">

      <div class="grid grid-cols-2 gap-5">
        <div>
          <h2 class="section-header">{{ $t('app.reports.subscriptions.overview.plans.title') }}</h2>
          <div class="section-body">
            <dl class="detail-list">
              <div v-for="entry in planCounts">
                <dt>{{ entry.name }}</dt>
                <dd>{{ entry.count }}</dd>
              </div>
            </dl>
          </div>
        </div>
        <div>
          <h2 class="section-header">{{ $t('app.reports.subscriptions.overview.schedules.title') }}</h2>
          <div class="section-body">
            <dl class="detail-list">
              <div v-for="entry in scheduleCounts">
                <dt>{{ entry.name }}</dt>
                <dd>{{ entry.count }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SubscriptionsOverview",
  data() {
    return {
      ready: false,
      planCounts: [],
      scheduleCounts: []
    }
  },
  mounted() {
    axios.get('/app/reports/subscriptions').then(response => {
      this.planCounts = response.data.subscriptions;
      this.scheduleCounts = response.data.schedule;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>