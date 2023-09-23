<template>
  <div>
    <h1 class="page-title mb-5">{{ $t('app.reports.dashboard.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="grid grid-cols-3 gap-5 mb-5">
        <div>
          <h2 class="section-header text-center">{{ $t('app.reports.dashboard.header.active_subscriptions') }}</h2>
          <div class="section-body text-7xl text-center">
            {{ responseData.header.active_subscriptions }}
          </div>
        </div>
        <div>
          <h2 class="section-header text-center">{{ $t('app.reports.dashboard.header.active_customers') }}</h2>
          <div class="section-body text-7xl text-center">
            {{ responseData.header.active_customers }}
          </div>
        </div>
        <div>
          <h2 class="section-header text-center">{{ $t('app.reports.dashboard.header.unpaid_invoices') }}</h2>
          <div class="section-body text-7xl text-center">
            {{ responseData.header.unpaid_invoices_count }} / {{ displayCurrency(responseData.header.unpaid_invoices_count) }} {{ currency }}
          </div>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-5">
        <div>
          <h2 class="section-header">{{ $t('app.reports.dashboard.subscription_count.title') }}</h2>
          <div class="section-body">
            <apexchart ref="analyticsChart" :series="subscriptionCountChartSeries" :options="subscriptionCountChartOptions"  height="400"   />
          </div>
        </div>
        <div class="grid">
          <div class="text-center">
            <h3 class="section-header">{{ $t('app.reports.dashboard.estimated_mrr') }}</h3>
            <div class="section-body">
              <span class="text-8xl">{{ displayCurrency(estimated_mrr) }} </span> {{ currency }}
            </div>
          </div>
          <div class="text-center">
            <h3 class="section-header">{{ $t('app.reports.dashboard.estimated_arr') }}</h3>
            <div class="section-body">
              <span class="text-8xl">{{ displayCurrency(estimated_arr) }} </span> {{ currency }}
            </div>
          </div>
        </div>
        <div>
          <h2 class="section-header">{{ $t('app.reports.dashboard.subscription_creation.title') }}</h2>
          <div class="section-body">
            <apexchart ref="analyticsChart" :series="subscriptionCreatedChartSeries" :options="subscriptionCreatedChartOptions"  height="400"   />
          </div>
        </div>
        <div>
          <h2 class="section-header">{{ $t('app.reports.dashboard.subscription_cancellation.title') }}</h2>
          <div class="section-body">
            <apexchart ref="analyticsChart" :series="subscriptionCancellationChartSeries" :options="subscriptionCancellationChartOptions"  height="400" />
          </div>
        </div>
        <div>
          <h2 class="section-header">{{ $t('app.reports.dashboard.payment_amount.title') }}</h2>
          <div class="section-body">
            <apexchart ref="analyticsChart" :series="paymentAmountChartSeries" :options="paymentAmountChartOptions"  height="400" />
          </div>
        </div>
        <div>
          <h2 class="section-header">{{ $t('app.reports.dashboard.refund_amount.title') }}</h2>
          <div class="section-body">
            <apexchart ref="analyticsChart" :series="refundAmountChartSeries" :options="refundAmountChartOptions"  height="400"   />
          </div>
        </div>
        <div>
          <h2 class="section-header">{{ $t('app.reports.dashboard.charge_back_amount.title') }}</h2>
          <div class="section-body">
            <apexchart ref="analyticsChart" :series="chargeBackAmountChartSeries" :options="chargeBackAmountChartOptions"  height="400"   />
          </div>
        </div>
    </div>

    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";

export default {
  name: "Dashboard",
  data() {
    return {
      ready: false,
      estimated_mrr: 0,
      estimated_arr: 0,
      currency: null,
      header: {},
      subscriptionCountChartSeries: [],
      subscriptionCountChartOptions: {
        title: {
          text: '',
          align: 'left'
        },
        chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: [],
        },
        yaxis: [
          {
            labels: {
              formatter: function(val) {
                return val.toFixed(0);
              }
            }
          }
        ]
      },
      subscriptionCreatedChartSeries: [],
      subscriptionCreatedChartOptions: {
        title: {
          text: '',
          align: 'left'
        },
        chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: [],
        },
        yaxis: [
          {
            labels: {
              formatter: function(val) {
                return val.toFixed(0);
              }
            }
          }
        ]
      },
      subscriptionCancellationChartSeries: [],
      subscriptionCancellationChartOptions: {
        title: {
          text: '',
          align: 'left'
        },
        chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: [],
        },
        yaxis: [
          {
            labels: {
              formatter: function(val) {
                return val.toFixed(0);
              }
            }
          }
        ]
      },
      paymentAmountChartSeries: [],
      paymentAmountChartOptions: {
        title: {
          text: '',
          align: 'left'
        },
        chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: [],
        },
        yaxis: [
          {
            labels: {
              formatter: function(val) {
                return val.toFixed(0);
              }
            }
          }
        ]
      },
      refundAmountChartSeries: [],
      refundAmountChartOptions: {
        title: {
          text: '',
          align: 'left'
        },
        chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: [],
        },
        yaxis: [
          {
            labels: {
              formatter: function(val) {
                return val.toFixed(0);
              }
            }
          }
        ]
      },
      chargeBackAmountChartSeries: [],
      chargeBackAmountChartOptions: {
        title: {
          text: '',
          align: 'left'
        },
        chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: [],
        },
        yaxis: [
          {
            labels: {
              formatter: function(val) {
                return val.toFixed(0);
              }
            }
          }
        ]
      },
      responseData: {}
    }
  },
  mounted() {
    axios.get('/app/stats').then(response => {
      this.ready = true;
      this.responseData = response.data;
      const viewName = 'monthly';
      this.setChartData(viewName)
    })
  },
  methods: {
    setChartData: function (viewName) {

      const subscriptionCountStats = this.convertStatToChartData(this.responseData.subscription_count[viewName]);
      this.subscriptionCountChartSeries = subscriptionCountStats.values;
      this.subscriptionCountChartOptions.xaxis.categories = subscriptionCountStats.categories;

      const subscriptionCreationStats = this.convertStatToChartData(this.responseData.subscription_creation[viewName]);
      this.subscriptionCreatedChartSeries = subscriptionCreationStats.values;
      this.subscriptionCreatedChartOptions.xaxis.categories = subscriptionCreationStats.categories;

      const subscriptionCancellationStats = this.convertStatToChartData(this.responseData.subscription_cancellation[viewName]);
      this.subscriptionCancellationChartSeries = subscriptionCancellationStats.values;
      this.subscriptionCancellationChartOptions.xaxis.categories = subscriptionCancellationStats.categories;

      const paymentAmountStats = this.convertMoneyToChartData(this.responseData.payment_amount[viewName]);
      this.paymentAmountChartSeries = paymentAmountStats.values;
      this.paymentAmountChartOptions.xaxis.categories = paymentAmountStats.categories;
      const refundAmountStats = this.convertMoneyToChartData(this.responseData.refund_amount[viewName]);
      this.refundAmountChartSeries = refundAmountStats.values;
      this.refundAmountChartOptions.xaxis.categories = refundAmountStats.categories;
      const chargeBackAmountStats = this.convertMoneyToChartData(this.responseData.charge_back_amount[viewName]);
      this.chargeBackAmountChartSeries = chargeBackAmountStats.values;
      this.chargeBackAmountChartOptions.xaxis.categories = chargeBackAmountStats.categories;

      this.currency = this.responseData.currency;
      this.estimated_mrr = this.responseData.estimated_mrr;
      this.estimated_arr = this.responseData.estimated_arr;
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

    convertMoneyToChartData: function (input) {
      var categories = []
      var values = []
      var currencies = [];
      var counter = 0;
      for (const [brand, subInput] of Object.entries(input)) {
        counter++;
        var subValues = {};
        for (const [date, subSubInput] of Object.entries(subInput)) {
          if (counter === 1) {
            categories.push(date)
          }
          for (const [currency, value] of Object.entries(subSubInput)) {
            if (subValues[currency] === undefined) {
              subValues[currency] = [];
            }
            subValues[currency].push(value)
          }
        }
        for (const [currency, theValues] of Object.entries(subValues)) {

          values.push({name: brand + " " + currency, data: theValues})
        }
      }
      return {categories, values};
    },
    displayCurrency: function (value) {
      return currency(value, { fromCents: true }).format({symbol: ''});
    },
  }
}
</script>

<style scoped>

</style>