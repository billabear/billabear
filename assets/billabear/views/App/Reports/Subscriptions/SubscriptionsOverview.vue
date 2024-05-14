<template>
  <div>
    <h1 class="page-title mb-5">{{ $t('app.reports.subscriptions.overview.title') }}</h1>
    <LoadingScreen :ready="ready">

      <div class="grid grid-cols-2 gap-5">
        <div class="card-body">
          <h2 class="section-header">{{ $t('app.reports.subscriptions.overview.plans.title') }}</h2>
          <div class="section-body">
            <apexchart type="pie" width="550" :options="overview.chartOptions" :series="overview.series"></apexchart>
          </div>
        </div>
        <div class="card-body">
          <h2 class="section-header">{{ $t('app.reports.subscriptions.overview.schedules.title') }}</h2>
          <div class="section-body">
            <apexchart type="pie" width="550" :options="paymentSchedules.chartOptions" :series="paymentSchedules.series"></apexchart>
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
  computed: {
    overview: function () {
      var labels = [];
      const data = this.convertStats(this.planCounts);
      return this.thedata(data)
    },
    paymentSchedules: function () {
      const data = this.convertStats(this.scheduleCounts);
      return this.thedata(data);
    }
  },
  methods: {
    thedata: function (data) {
      return {
        series: data.values,
        chartOptions: {
          chart: {
            width: 700,
            type: 'pie',
          },
          labels: data.categories,
          responsive: [{
            breakpoint: 480,
            options: {
              chart: {
                width: 700
              },
              legend: {
                position: 'bottom'
              }
            }
          }]
        },
      }
    },
    convertStats: function (input) {

      var categories = []
      var values = []

      for (var i = 0; i < input.length; i++) {
        categories.push(input[i].name);
        values.push(input[i].count);
      }
      console.log(categories);

      return {categories, values};
    },
    convertStatToChartData: function (input) {
      var categories = []
      var values = []
      var counter = 0;
      for (const [brand, subInput] of Object.entries(input)) {
        var subValues = [];
        counter++;
        for (const [key, value] of Object.entries(subInput)) {
          if (counter === 1) {
            categories.push(key)
          }
          subValues.push(value)
        }
        values.push({name: brand, data: subValues})
      }
      return {categories, values};
    },
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