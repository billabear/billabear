<template>
  <div>
    <h1 class="page-title">{{ $t('app.reports.financial.lifetime.title') }}</h1>


    <div class="grid grid-cols-2 gap-5">

      <div class="card-body">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.reports.financial.lifetime.filters.country') }}
          </label>
          <p class="form-field-error" v-if="errors.country != undefined">{{ errors.country }}</p>
          <CountrySelect v-model="filters.country" />
          <p class="form-field-help">{{ $t('app.reports.financial.lifetime.help_info.country') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="payment_schedule">
            {{ $t('app.reports.financial.lifetime.filters.payment_schedule') }}
          </label>
          <p class="form-field-error" v-if="errors.payment_schedule != undefined">{{ errors.payment_schedule }}</p>
          <select v-model="filters.payment_schedule" class="form-field">
            <option :value="null"></option>
            <option value="week">{{ $t('app.reports.financial.lifetime.schedules.week') }}</option>
            <option value="month">{{ $t('app.reports.financial.lifetime.schedules.month') }}</option>
            <option value="year">{{ $t('app.reports.financial.lifetime.schedules.year') }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.reports.financial.lifetime.help_info.payment_schedule') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="payment_schedule">
            {{ $t('app.reports.financial.lifetime.filters.subscription_plan') }}
          </label>
          <p class="form-field-error" v-if="errors.subscription_plan != undefined">{{ errors.subscription_plan }}</p>
          <select v-model="filters.subscription_plan" class="form-field">
            <option :value="null"></option>
            <option v-for="plan in stats.plans" :value="plan.id">{{plan.name}}</option>
          </select>
          <p class="form-field-help">{{ $t('app.reports.financial.lifetime.help_info.subscription_plan') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="brand">
            {{ $t('app.reports.financial.lifetime.filters.brand') }}
          </label>
          <p class="form-field-error" v-if="errors.brand != undefined">{{ errors.brand }}</p>
          <select v-model="filters.brand" class="form-field">
            <option :value="null"></option>
            <option v-for="brand in stats.brands" :value="brand.id">{{brand.name}}</option>
          </select>
          <p class="form-field-help">{{ $t('app.reports.financial.lifetime.help_info.brand') }}</p>
        </div>


        <div class="mt-5">
          <SubmitButton :in-progress="!ready" @click="sendFilters">{{ $t('app.reports.financial.lifetime.submit') }}</SubmitButton>
        </div>
      </div>

      <div>
        <LoadingScreen :ready="ready">
          <div class="card-body">
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.reports.financial.lifetime.customer_count') }}</dt>
                <dd>{{ stats.customer_count }}</dd>
              </div>
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
    </div>

    <div class="mt-5 card-body">
      <LoadingScreen :ready="ready">

        <apexchart  type="line" :series="chartData" :options="chartOptions" />
      </LoadingScreen>
    </div>

  </div>
</template>

<script>
import axios from "axios";
import Currency from "../../../../components/app/Currency.vue";
import CountrySelect from "../../../../components/app/Forms/CountrySelect.vue";
import {Select} from "flowbite-vue";

export default {
  name: "LifetimeReport",
  components: {Select, CountrySelect, Currency},
  data() {
    return {
      ready: false,
      stats: {},
      errors: {},
      filters: {country: null},
      chartOptions: {
        labels: [],
        xaxis: {
          type: 'datetime'
        },
        yaxis: [{
          title: {
            text:  this.$t('app.reports.financial.lifetime.chart.lifetime_values'),
          },

        }, {
          opposite: true,
          title: {
            text:  this.$t('app.reports.financial.lifetime.chart.customer_counts'),
          }
        }]
      },
      chartData: [],
    }
  },
  mounted() {
    axios.get("/app/stats/lifetime").then(response => {
      this.stats = response.data;
      this.chartOptions.labels = response.data.graph_data.labels;
      this.chartData = this.convertChartData(response.data.graph_data);
      this.ready = true;

    })
  },
  methods: {
    sendFilters: function () {
      this.ready = false;
      var filtersString = '';

      for (const [key, value] of Object.entries(this.filters)) {
        if (value == null || value == "null" || value === undefined) {
          continue;
        }
        filtersString = key+'='+value;
      }

      axios.get("/app/stats/lifetime?"+filtersString).then(response => {
        this.stats = response.data;
        this.chartOptions.labels = response.data.graph_data.labels;
        this.chartData = this.convertChartData(response.data.graph_data);
        this.ready = true;
      })
    },
    convertChartData: function (input) {

      const lifetimeValues = {
        name: this.$t('app.reports.financial.lifetime.chart.lifetime_values'),
        type: 'column',
        data: input.lifetime_values,
      }
      const customerCount = {
        name: this.$t('app.reports.financial.lifetime.chart.customer_counts'),
        type: 'line',
        data: input.customer_counts,
      }

      return [lifetimeValues, customerCount];
    },
  }
}
</script>

<style scoped>

</style>