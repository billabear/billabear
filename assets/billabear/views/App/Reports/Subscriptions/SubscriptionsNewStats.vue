<template>
  <div>
    <h1 class="page-title mb-5">{{ $t('app.reports.subscriptions.new_stats.title') }}</h1>
    <LoadingScreen :ready="ready">
      <div class="text-end my-5">
        <div class="bg-white rounded-3xl inline p-3">
          <div class="chart-button inline p-3 rounded-3xl" @click="setChartData('monthly')" :class="{'chart-button-selected': viewName === 'monthly'}">
            {{ $t('app.reports.subscriptions.new_stats.buttons.monthly') }}
          </div>
          <div class="chart-button inline p-3 rounded-3xl" @click="setChartData('yearly')" :class="{'chart-button-selected': viewName === 'yearly'}">
            {{ $t('app.reports.subscriptions.new_stats.buttons.yearly') }}
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="section-body">
          <apexchart height="500" type="bar" :options="chartOptions" :series="chartSeries"></apexchart>
        </div>
      </div>
      
      <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mt-5">
        <div class="stat">
          <h2 class="stat-header">{{ $t('app.reports.subscriptions.new_stats.totals.existing') }}</h2>
          <div class="stat-body text-3xl">
            {{ totals.existing }}
          </div>
        </div>
        <div class="stat">
          <h2 class="stat-header">{{ $t('app.reports.subscriptions.new_stats.totals.new') }}</h2>
          <div class="stat-body text-3xl">
            {{ totals.new }}
          </div>
        </div>
        <div class="stat">
          <h2 class="stat-header">{{ $t('app.reports.subscriptions.new_stats.totals.upgrades') }}</h2>
          <div class="stat-body text-3xl">
            {{ totals.upgrades }}
          </div>
        </div>
        <div class="stat">
          <h2 class="stat-header">{{ $t('app.reports.subscriptions.new_stats.totals.downgrades') }}</h2>
          <div class="stat-body text-3xl">
            {{ totals.downgrades }}
          </div>
        </div>
        <div class="stat">
          <h2 class="stat-header">{{ $t('app.reports.subscriptions.new_stats.totals.cancellations') }}</h2>
          <div class="stat-body text-3xl">
            {{ totals.cancellations }}
          </div>
        </div>
        <div class="stat">
          <h2 class="stat-header">{{ $t('app.reports.subscriptions.new_stats.totals.reactivations') }}</h2>
          <div class="stat-body text-3xl">
            {{ totals.reactivations }}
          </div>
        </div>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SubscriptionsNewStats",
  data() {
    return {
      ready: false,
      viewName: 'monthly',
      months: [],
      totals: {
        existing: 0,
        new: 0,
        upgrades: 0,
        downgrades: 0,
        cancellations: 0,
        reactivations: 0
      },
      chartOptions: {
        chart: {
          type: 'bar',
          height: 500,
          stacked: true,
          toolbar: {
            show: true
          },
          zoom: {
            enabled: false
          }
        },
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10,
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'last',
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
          categories: []
        },
        yaxis: {
          labels: {
            formatter: function(val) {
              return val.toFixed(0);
            }
          }
        },
        legend: {
          position: 'top',
          horizontalAlign: 'left',
          offsetX: 40
        },
        colors: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#6B7280', '#8B5CF6']
      },
      chartSeries: []
    }
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios.get('/app/reports/subscriptions/new').then(response => {
        this.months = response.data.months;
        this.calculateTotals();
        this.setChartData('monthly');
        this.ready = true;
      }).catch(error => {
        console.error('Error fetching subscription stats:', error);
      });
    },
    setChartData(viewName) {
      this.viewName = viewName;
      
      // Filter months based on view (monthly = last 12 months, yearly = group by year)
      let filteredMonths = this.months;
      if (viewName === 'yearly') {
        // Group by year and aggregate data
        const yearlyData = {};
        this.months.forEach(month => {
          const year = month.month.substring(0, 4);
          if (!yearlyData[year]) {
            yearlyData[year] = {
              month: year,
              existing: 0,
              new: 0,
              upgrades: 0,
              downgrades: 0,
              cancellations: 0,
              reactivations: 0
            };
          }
          yearlyData[year].existing += month.existing;
          yearlyData[year].new += month.new;
          yearlyData[year].upgrades += month.upgrades;
          yearlyData[year].downgrades += month.downgrades;
          yearlyData[year].cancellations += month.cancellations;
          yearlyData[year].reactivations += month.reactivations;
        });
        filteredMonths = Object.values(yearlyData);
      } else {
        // Use last 12 months for monthly view
        filteredMonths = this.months.slice(-12);
      }
      
      // Prepare chart data
      const categories = filteredMonths.map(month => month.month);
      
      const series = [
        {
          name: this.$t('app.reports.subscriptions.new_stats.labels.existing'),
          data: filteredMonths.map(month => month.existing)
        },
        {
          name: this.$t('app.reports.subscriptions.new_stats.labels.new'),
          data: filteredMonths.map(month => month.new)
        },
        {
          name: this.$t('app.reports.subscriptions.new_stats.labels.upgrades'),
          data: filteredMonths.map(month => month.upgrades)
        },
        {
          name: this.$t('app.reports.subscriptions.new_stats.labels.downgrades'),
          data: filteredMonths.map(month => month.downgrades)
        },
        {
          name: this.$t('app.reports.subscriptions.new_stats.labels.cancellations'),
          data: filteredMonths.map(month => month.cancellations)
        },
        {
          name: this.$t('app.reports.subscriptions.new_stats.labels.reactivations'),
          data: filteredMonths.map(month => month.reactivations)
        }
      ];
      
      this.chartOptions.xaxis.categories = categories;
      this.chartSeries = series;
    },
    calculateTotals() {
      // Calculate totals for the last 12 months
      const last12Months = this.months.slice(-12);
      
      this.totals = {
        existing: last12Months.reduce((sum, month) => sum + month.existing, 0),
        new: last12Months.reduce((sum, month) => sum + month.new, 0),
        upgrades: last12Months.reduce((sum, month) => sum + month.upgrades, 0),
        downgrades: last12Months.reduce((sum, month) => sum + month.downgrades, 0),
        cancellations: last12Months.reduce((sum, month) => sum + month.cancellations, 0),
        reactivations: last12Months.reduce((sum, month) => sum + month.reactivations, 0)
      };
    }
  }
}
</script>

<style scoped>
.stat {
  @apply bg-white text-black p-3 rounded-lg shadow;
}

.stat-header {
  @apply text-sm mb-2;
}

.stat-body {
  @apply mt-2;
}
</style>