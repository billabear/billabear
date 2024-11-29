<template>
  <div>
    <PageTitle>{{ $t('app.metric.update.title') }}</PageTitle>
    <LoadingScreen :ready="loaded">
      <div class="card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.metric.create.fields.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field" v-model="metric.name" />
          <p class="form-field-help">{{ $t('app.metric.create.help_info.name') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="code">
            {{ $t('app.metric.create.fields.code') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.code }}</p>
          <p>{{ metric.code }}</p>
          <p class="form-field-help">{{ $t('app.metric.create.help_info.code') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="aggregation_method">
            {{ $t('app.metric.create.fields.aggregation_method') }}
          </label>
          <p class="form-field-error" v-if="errors.aggregationMethod != undefined">{{ errors.aggregationMethod }}</p>

          <select class="form-field" v-model="metric.aggregation_method">
            <option value="count">{{ $t('app.metric.create.aggregation_methods.count') }}</option>
            <option value="sum">{{ $t('app.metric.create.aggregation_methods.sum') }}</option>
            <option value="latest">{{ $t('app.metric.create.aggregation_methods.latest') }}</option>
            <option value="unique_count">{{ $t('app.metric.create.aggregation_methods.unique_count') }}</option>
            <option value="max">{{ $t('app.metric.create.aggregation_methods.max') }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.metric.create.help_info.aggregation_method') }}</p>
        </div>

        <div class="form-field-ctn" v-if="showAggregationProperty">
          <label class="form-field-lbl" for="aggregation_property">
            {{ $t('app.metric.create.fields.aggregation_property') }}
          </label>
          <p class="form-field-error" v-if="errors.type != undefined">{{ errors.type }}</p>
          <input type="text" class="form-field" v-model="metric.aggregation_property">
          <p class="form-field-help">{{ $t('app.metric.create.help_info.aggregation_property') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.metric.create.fields.filters') }}
          </label>

          <table class="w-1/2">
            <thead>
            <tr>
              <th>{{ $t('app.metric.create.filter.name') }}</th>
              <th>{{ $t('app.metric.create.filter.value') }}</th>
              <th>{{ $t('app.metric.create.filter.type') }}</th>
              <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(filter, key) in metric.filters">
              <td><input type="text" class="form-field" v-model="filter.name"></td>
              <td><input type="text" class="form-field" v-model="filter.value"></td>
              <td><select class="form-field" v-model="filter.type">
                <option value="inclusive">{{ $t('app.metric.create.filter_type.inclusive') }}</option>
                <option value="exclusive">{{ $t('app.metric.create.filter_type.exclusive') }}</option>
              </select></td>
              <td><button class="btn--danger" @click="removeFilter(key)"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
            <tr v-if="metric.filters.length === 0">
              <td colspan="3" class="text-center">{{ $t('app.metric.create.filter.no_filters') }}</td>
            </tr>
            </tbody>
          </table>

          <button class="btn--main mt-2" @click="addFilter"><i class="fa-solid fa-plus"></i></button>
          <p class="form-field-help">{{ $t('app.metric.create.help_info.filters') }}</p>

        </div>
      </div>
      <div class="mt-3">
        <SubmitButton @click="send" :in-progress="sending">{{ $t('app.metric.update.update_button') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import PageTitle from "../../../components/app/Ui/Typography/PageTitle.vue";
import axios from "axios";

export default {
  name: "MetricUpdate",
  components: {PageTitle},
  data() {
    return {
      errors: {},
      loaded: false,
      metric: {},
      sending: false,
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get("/app/metric/"+id+"/read").then(response => {
      this.metric = response.data;
      this.loaded = true;
    })
  },
  computed: {
    showAggregationProperty: function() {
      return !(this.metric.aggregation_method === 'count' || this.metric.aggregation_method === 'sum');
    }
  },
  methods: {
    addFilter: function() {
      this.metric.filters.push({});
    },
    removeFilter: function(key) {
      this.metric.filters.splice(key, 1)
    },
    send: function() {
      this.errors = {};
      this.sending = true;
      const id = this.$route.params.id;
      axios.post("/app/metric/"+id+"/update", this.metric).then(response => {
        this.$router.push({'name': 'app.metric.view', params: {id: response.data.id}})
      }).catch(error => {
        if (error.response !== undefined && error.response.data !== undefined && error.response.data.errors !== undefined) {
          this.errors = error.response.data.errors;
        }
        this.sending = false;
      })
    }
  }
}
</script>

<style scoped>

</style>
