<template>

  <div>
    <h1 class="page-title mb-5">{{ $t('app.reports.subscriptions.churn.title') }}</h1>
    <LoadingScreen :ready="ready">


      <div class="text-end my-5">
        <div class="chart-button" @click="setChartData('daily')" :class="{'chart-button-selected': viewName === 'daily'}">
          {{ $t('app.reports.subscriptions.churn.buttons.daily') }}
        </div>
        <div class="chart-button" @click="setChartData('monthly')" :class="{'chart-button-selected': viewName === 'monthly'}">
          {{ $t('app.reports.subscriptions.churn.buttons.monthly') }}
        </div>
        <div class="chart-button" @click="setChartData('yearly')" :class="{'chart-button-selected': viewName === 'yearly'}">
          {{ $t('app.reports.subscriptions.churn.buttons.yearly') }}
        </div>
      </div>
        <div class="card-body">
          <div class="section-body">
            <apexchart v-if="viewName == 'daily'" height="500" type="bar" :options="overview.chartOptions" :series="overview.series"></apexchart>
            <apexchart v-if="viewName == 'monthly'" height="500" type="bar" :options="overview.chartOptions" :series="overview.series"></apexchart>
            <apexchart v-if="viewName == 'yearly'" height="500" type="bar" :options="overview.chartOptions" :series="overview.series"></apexchart>
          </div>
        </div>

    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SubscriptionsChurn",
  data() {
      return {
        chartData: {},
        ready: false,
        overview: {chartOptions: {xaxis: {categories: {}}}, series: {}},
        viewName: 'daily',
        loaded: false,
      }
  },
  mounted() {
    axios.get('/app/reports/subscriptions/churn').then(response => {
      this.chartData = response.data;
      this.setChartData('daily')
      this.ready = true;
    })
  },
  methods: {

    setChartData: function (viewName) {
      this.viewName = viewName;
      const data = this.convertStatToChartData(this.chartData[viewName]);
      console.log(data)
      this.overview.series = [{name: 'churn', data: data.values}];
      this.overview.chartOptions.xaxis.categories = data.categories;
      this.loaded = true;
    },
    convertStatToChartData: function (input) {
      var categories = []
      var values = [];

      for (var i = 0; input.length > i; i++) {
        let data = input[i];
        let label = data.dayDate+'-'+data.monthDate+'-'+data.yearDate;
        categories.push(data.dayDate+'-'+data.monthDate+'-'+data.yearDate);
        values.push(data.count)
      }
      let valuesOutput = {data: values}

      return {categories, values};
    },
  }
}
</script>

<style scoped>

</style>