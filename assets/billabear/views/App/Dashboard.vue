<template>
  <div class="p-5">
    <h1 class="page-title mb-5">{{ $t('app.reports.dashboard.title') }}</h1>

    <div v-if="canSeeStats">
      <OnboardingMenu v-if="show_onboarding" />

      <LoadingScreen :ready="ready">
        <div class="grid grid-cols-4 gap-5 mb-5">
          <div class="stat">
            <h2 class="stat-header">{{ $t('app.reports.dashboard.header.active_subscriptions') }}</h2>
            <div class="stat-body text-3xl">
              {{ responseData.header.active_subscriptions }}
            </div>
          </div>
          <div class="stat">
            <h2 class="stat-header">{{ $t('app.reports.dashboard.header.active_customers') }}</h2>
            <div class="stat-body text-3xl">
              {{ responseData.header.active_customers }}
            </div>
          </div>
          <div class="stat">
            <h2 class="stat-header">{{ $t('app.reports.dashboard.header.unpaid_invoices') }}</h2>
            <div class="stat-body">
              <span class="text-3xl">{{ responseData.header.unpaid_invoices_count }}</span> / {{ displayCurrency(responseData.header.unpaid_invoices_amount) }} {{ currency }}
            </div>
          </div>
          <div class="stat">
            <h3 class="stat-header">{{ $t('app.reports.dashboard.estimated_mrr') }}</h3>
            <div class="stat-body">
              <span class="text-3xl">{{ displayCurrency(estimated_mrr) }} </span> {{ currency }}
            </div>
          </div>
        </div>
        <div class="text-end my-5">
          <div class="chart-button" @click="setChartData('daily')" :class="{'chart-button-selected': viewName === 'daily'}">
            {{ $t('app.reports.dashboard.buttons.daily') }}
          </div>
          <div class="chart-button" @click="setChartData('monthly')" :class="{'chart-button-selected': viewName === 'monthly'}">
            {{ $t('app.reports.dashboard.buttons.monthly') }}
          </div>
          <div class="chart-button" @click="setChartData('yearly')" :class="{'chart-button-selected': viewName === 'yearly'}">
            {{ $t('app.reports.dashboard.buttons.yearly') }}
          </div>
        </div>
        <div class="card-body">
          <div  class="">
            <h2 class="chart-title">{{ $t('app.reports.dashboard.subscription_count.title') }}</h2>
            <div class="section-body">
              <apexchart ref="analyticsChart" :series="subscriptionCountChartSeries" :options="subscriptionCountChartOptions"  height="400"   />
            </div>
          </div>
        </div>
      </LoadingScreen>
    </div>
    <div v-else class="grid grid-cols-3 gap-5">
      <div class="card-body text-center">
        <router-link :to="{name: 'app.customer.list'}" class="text-4xl underline text-blue-500">{{ $t('app.reports.dashboard.links.customers') }}</router-link>
      </div>
      <div class="card-body text-center">
        <router-link :to="{name: 'app.subscription'}" class="text-4xl underline text-blue-500">{{ $t('app.reports.dashboard.links.subscriptions') }}</router-link>
      </div>
      <div class="card-body text-center">
        <router-link :to="{name: 'app.invoices.list'}" class="text-4xl underline text-blue-500">{{ $t('app.reports.dashboard.links.invoices') }}</router-link>
      </div>
    </div>
  </div>
  <pre>
    {{ trans }}
  </pre>
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import WorldMap from "../../components/app/Graphs/WorldMap.vue";
import {mapState} from "vuex";
import OnboardingMenu from "../../components/app/Onboarding/OnboardingMenu.vue";

import {ENGLISH_TRANSLATIONS} from "../../translations/en";

export default {
  name: "Dashboard",
  components: {OnboardingMenu, WorldMap},
  data() {
    return {
      trans: ENGLISH_TRANSLATIONS,
      ready: false,
      estimated_mrr: 0,
      estimated_arr: 0,
      currency: null,
      header: {},
      viewName: 'monthly',
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
  computed: {
    ...mapState('onboardingStore', [
      'show_onboarding'
    ]),
    canSeeStats: function(){
      var data;
      try {
        data = JSON.parse(localStorage.getItem('user'))
      } catch (e) {
        return false;
      }

      if (data.roles.length === 1 && data.roles.includes('ROLE_CUSTOMER_SUPPORT')) {
        return false;
      }

      return true;
    }
  },
  methods: {
    setChartData: function (viewName) {
      this.viewName = viewName;
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

.stat {
  @apply bg-gray-200 text-black p-3 rounded-lg shadow-lg dark:text-white dark:bg-gray-700 ;
}

.stat-header {
  @apply mb-5;
}

.stat-body {
  @apply mt-5;
}

</style>
