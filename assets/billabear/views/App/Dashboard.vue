<template>
  <div class="">
    <h1 class="page-title">{{ $t('app.reports.dashboard.title') }}</h1>

    <div v-if="canSeeStats">
      <OnboardingMenu v-if="show_onboarding" />

      <LoadingScreen :ready="ready">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
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
          <div class="stat" v-if="viewName !== 'yearly'">
            <h3 class="stat-header">{{ $t('app.reports.dashboard.estimated_mrr') }}</h3>
            <div class="stat-body">
              <span class="text-3xl">{{ displayCurrency(estimated_mrr) }} </span> {{ currency }}
            </div>
          </div>
          <div class="stat" v-else>
            <h3 class="stat-header">{{ $t('app.reports.dashboard.estimated_arr') }}</h3>
            <div class="stat-body">
              <span class="text-3xl">{{ displayCurrency(estimated_arr) }} </span> {{ currency }}
            </div>
          </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
          <div class="col-span-2">
            <div class="grid grid-cols-2">
              <div class="my-5">

                <div class="bg-white rounded-3xl inline p-3">
                  <div class="chart-button inline p-3 rounded-3xl " @click="setChart('subscriptions')" :class="{'chart-button-selected': chart === 'subscriptions'}">
                    {{ $t('app.reports.dashboard.buttons.subscriptions') }}
                  </div>
                  <div class="chart-button inline p-3 rounded-3xl " @click="setChart('payments')" :class="{'chart-button-selected': chart === 'payments'}">
                    {{ $t('app.reports.dashboard.buttons.payments') }}
                  </div>
                </div>
              </div>
              <div class="text-end my-5">
                <div class="bg-white rounded-3xl inline p-3">
                  <div class="chart-button inline p-3 rounded-3xl " @click="setChartData('daily')" :class="{'chart-button-selected': viewName === 'daily'}">
                    {{ $t('app.reports.dashboard.buttons.daily') }}
                  </div>
                  <div class="chart-button inline p-3 rounded-3xl " @click="setChartData('monthly')" :class="{'chart-button-selected': viewName === 'monthly'}">
                    {{ $t('app.reports.dashboard.buttons.monthly') }}
                  </div>
                  <div class="chart-button inline p-3 rounded-3xl " @click="setChartData('yearly')" :class="{'chart-button-selected': viewName === 'yearly'}">
                    {{ $t('app.reports.dashboard.buttons.yearly') }}
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body" v-if="chart=='subscriptions'">
              <div  class="">
                <h2 class="chart-title">{{ $t('app.reports.dashboard.subscription_count.title') }}</h2>
                <div class="section-body">
                  <apexchart ref="analyticsChart" :series="subscriptionCountChartSeries" :options="subscriptionCountChartOptions"  height="400"   />
                </div>
              </div>
            </div>
            <div class="card-body" v-if="chart=='payments'">
              <div  class="">
                <h2 class="chart-title">{{ $t('app.reports.dashboard.payments.title') }}</h2>
                <div class="section-body">
                  <apexchart ref="analyticsChart" :series="paymentAmountChartSeries" :options="paymentAmountChartOptions"  height="400"   />
                </div>
              </div>
            </div>
            <div class="flex card-body h-[430px] text-center place-content-center" v-if="chart=='loading'">
              <LoadingMessage class="place-content-center">{{ $t('app.reports.dashboard.loading_chart') }}</LoadingMessage>
            </div>
          </div>
          <div class="">
            <TabGroup>
              <TabList class="block w-full bg-white rounded-3xl my-3 flex justify-center ">
                <Tab v-slot="{ selected }">
                  <button :class="['chart-button inline p-2 rounded-3xl', selected ? 'chart-button-selected' : '' ]">
                    {{ $t('app.reports.dashboard.latest_customers.title') }}
                  </button>
                </Tab>
                <Tab v-slot="{ selected }">
                  <button :class="['chart-button inline p-2 rounded-3xl', selected ? 'chart-button-selected' : '' ]">
                    {{ $t('app.reports.dashboard.latest_events.title') }}
                  </button>
                </Tab>
                <Tab v-slot="{ selected }">
                  <button :class="['chart-button inline p-2 rounded-3xl', selected ? 'chart-button-selected' : '' ]">
                    {{ $t('app.reports.dashboard.latest_payments.title') }}
                  </button>
                </Tab>
              </TabList>
              <TabPanels>
                <TabPanel>
                  <div class="card-body overflow-auto">
                    <h2 class="section-header"></h2>

                    <div class="mt-2">
                      <table class="list-table">
                        <thead>
                        <tr>
                          <th>{{ $t('app.reports.dashboard.latest_customers.list.email') }}</th>
                          <th>{{ $t('app.reports.dashboard.latest_customers.list.creation_date') }}</th>
                          <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="customer in customers">
                          <td><router-link  :to="{name: 'app.customer.view', params: {id: customer.id}}">{{ customer.email }}</router-link></td>
                          <td>{{ $filters.moment(customer.created_at, 'lll') }}</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </TabPanel>
                <TabPanel>
                  <div class="card-body overflow-auto">

                    <div class="mt-2">
                      <table class="list-table">
                        <thead>
                        <tr>
                          <th>{{ $t('app.reports.dashboard.latest_events.list.event_type') }}</th>
                          <th>{{ $t('app.reports.dashboard.latest_events.list.customer') }}</th>
                          <th>{{ $t('app.reports.dashboard.latest_events.list.creation_date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="event in events">
                          <td>{{ event.type }}</td>
                          <td><router-link  :to="{name: 'app.customer.view', params: {id: event.subscription.customer.id}}">{{ event.subscription.customer.email }}</router-link></td>
                          <td>{{ $filters.moment(event.created_at, 'lll') }}</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>

                  </div>
                </TabPanel>
                <TabPanel>
                  <div class="card-body overflow-auto">
                    <div class="mt-2">
                      <table class="list-table">
                        <thead>
                        <tr>
                          <th>{{ $t('app.reports.dashboard.latest_payments.list.amount') }}</th>
                          <th>{{ $t('app.reports.dashboard.latest_payments.list.customer') }}</th>
                          <th>{{ $t('app.reports.dashboard.latest_payments.list.creation_date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="payment in payments">
                          <td>{{ payment.currency }} {{ displayCurrency(payment.amount) }}</td>
                          <td><router-link  :to="{name: 'app.customer.view', params: {id: payment.customer.id}}">{{ payment.customer.email }}</router-link></td>
                          <td>{{ $filters.moment(payment.created_at, 'lll') }}</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </TabPanel>
              </TabPanels>
            </TabGroup>
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
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import WorldMap from "../../components/app/Graphs/WorldMap.vue";
import {mapState} from "vuex";
import OnboardingMenu from "../../components/app/Onboarding/OnboardingMenu.vue";
import {TabGroup, Tab, TabList, TabPanel, TabPanels} from "@headlessui/vue";

export default {
  name: "Dashboard",
  components: {Tab, TabPanels, TabPanel, TabList, TabGroup, OnboardingMenu, WorldMap},
  data() {
    return {
      ready: false,
      estimated_mrr: 0,
      estimated_arr: 0,
      currency: null,
      header: {},
      viewName: 'monthly',
      chart: 'subscriptions',
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
      paymentAmountChartSeries: [],
      paymentAmountChartOptions: {
        title: {
          text: '',
          align: 'left'
        },
        chart: {
          height: 350,
          type: 'bar',
          stacked: true,
          toolbar: {
            show: true
          },
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false,
          formatter: function (val) {
            return `${currency(val, { fromCents: true }).format({symbol: ''})}`;
          },
          style: {
            fontSize: '13px',
            fontWeight: 900
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10,
            borderRadiusApplication: 'end', // 'around', 'end'
            borderRadiusWhenStacked: 'last', // 'all', 'last'
            dataLabels: {
              total: {
                enabled: true,
                style: {
                  fontSize: '13px',
                  fontWeight: 900
                }
              }
            }
          },
        },
        xaxis: {
          categories: [],
        },
        yaxis: [
          {
            labels: {
              formatter: function(val) {
                return `${currency(val, { fromCents: true }).format({symbol: ''})}`;
              }
            }
          }
        ]
      },
      responseData: {},
      customers: [],
      events: [],
      payments: []
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
    setChart: function (chartName) {
      this.chart = chartName;
      this.setChartData(this.viewName)
    },
    setChartData: function (viewName) {
      const orgChart = this.chart;
      this.chart = 'loading';

      let that = this;
      setTimeout(function() {
        var data;

        if (orgChart === 'subscriptions') {
          data = that.responseData.subscription_count;
          const subscriptionCountStats = that.convertStatToChartData(data[viewName]);
          that.subscriptionCountChartSeries = subscriptionCountStats.values;
          that.subscriptionCountChartOptions.xaxis.categories = subscriptionCountStats.categories;
          console.log("Testsfdsf")
        } else {
          data = that.responseData.revenue_stats[viewName];
          that.paymentAmountChartSeries = data.series;
          that.paymentAmountChartOptions.xaxis.categories  = data.xaxis;
        }

        that.viewName = viewName;
        that.chart = orgChart;
      }, 100);
      this.currency = this.responseData.currency;
      this.estimated_mrr = this.responseData.estimated_mrr;
      this.estimated_arr = this.responseData.estimated_arr;
      this.customers = this.responseData.latest_customers;
      this.events = this.responseData.subscription_events;
      this.payments = this.responseData.latest_payments;
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
  @apply bg-white text-black p-3 rounded-lg shadow;
}

.stat-header {
  @apply mb-5;
}

.stat-body {
  @apply mt-5;
}


</style>
