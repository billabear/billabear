<template>
  <div>
    <div class="grid grid-cols-2">
      <div><PageTitle>{{ $t('app.metric.view.title') }}</PageTitle></div>
      <div class="text-end pt-5" v-if="loaded">
        <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">
          <router-link :to="{name: 'app.metric.update', params: {id: this.metric.id}}" class="list-btn">{{ $t('app.metric.view.update') }}</router-link>
        </RoleOnlyView>
      </div>
    </div>

    <LoadingScreen :ready="loaded">
      <div class="card-body">

          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.metric.view.main.name') }}</dt>
              <dd>{{ metric.name }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.metric.view.main.code') }}</dt>
              <dd>{{ metric.code }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.metric.view.main.type') }}</dt>
              <dd>{{ metric.type }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.metric.view.main.aggregation_method') }}</dt>
              <dd>{{ metric.aggregation_method }}</dd>
            </div>
            <div v-if="metric.aggregation_property != null">
              <dt>{{ $t('app.metric.view.main.aggregation_property') }}</dt>
              <dd>{{ metric.aggregation_property }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.metric.view.main.event_ingestion') }}</dt>
              <dd>{{ metric.event_ingestion }}</dd>
            </div>
          </dl>
      </div>
      <div class="card-body mt-3" v-if="metric.filters.length > 0">
        <h2 class="text-2xl">{{ $t('app.metric.view.filters.title') }}</h2>
        <table class="w-1/2">
          <thead>
            <tr class="border-b border-black">
              <th class="text-left">{{ $t('app.metric.view.filters.name') }}</th>
              <th class="text-left">{{ $t('app.metric.view.filters.value') }}</th>
              <th class="text-left">{{ $t('app.metric.view.filters.type') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="filter in metric.filters">
              <td>{{ filter.name }}</td>
              <td>{{ filter.value }}</td>
              <td>{{ $t('app.metric.view.filters.' + filter.type) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </LoadingScreen>


  </div>
</template>

<script>
import PageTitle from "../../../components/app/Ui/Typography/PageTitle.vue";
import axios from "axios";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "MetricView",
  components: {RoleOnlyView, PageTitle},
  data() {
    return {
      loaded: false,
      metric: {}
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get("/app/metric/"+id+"/read").then(response => {
      this.metric = response.data;
      this.loaded = true;
    })
  }
}
</script>

<style scoped>

</style>
