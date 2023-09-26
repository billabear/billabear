<template>
  <div>
    <h1 class="page-title">{{ $t('app.subscription.mass_change.view.title') }}</h1>
    <LoadingScreen :ready="ready">
      <div class="mt-5 card-body">
        <h3>{{ $t('app.subscription.mass_change.view.criteria.title') }}</h3>


        <dl class="detail-list">
          <div v-if="mass_change.target_plan !== null">
            <dt>{{ $t('app.subscription.mass_change.view.criteria.plan') }}</dt>
            <dd>{{ mass_change.target_plan.name }}</dd>
          </div>
          <div v-if="mass_change.target_price !== null">
            <dt>{{ $t('app.subscription.mass_change.view.criteria.price') }}</dt>
            <dd>{{ mass_change.target_price.display_value }}</dd>
          </div>
          <div v-if="mass_change.target_country !== null">
            <dt>{{ $t('app.subscription.mass_change.view.criteria.country') }}</dt>
            <dd>{{ mass_change.target_country }}</dd>
          </div>
          <div v-if="mass_change.target_brand !== null">
            <dt>{{ $t('app.subscription.mass_change.view.criteria.brand') }}</dt>
            <dd>{{ mass_change.target_brand.name }}</dd>
          </div>
        </dl>
      </div>
      <div class="mt-5 card-body">
        <h3>{{ $t('app.subscription.mass_change.view.new_values.title') }}</h3>
        <dl class="detail-list">
          <div v-if="mass_change.new_plan !== null">
            <dt>{{ $t('app.subscription.mass_change.view.new_values.plan') }}</dt>
            <dd>{{ mass_change.new_plan.name }}</dd>
          </div>
          <div v-if="mass_change.new_price !== null">
            <dt>{{ $t('app.subscription.mass_change.view.new_values.price') }}</dt>
            <dd>{{ mass_change.new_price.display_value }}</dd>
          </div>
        </dl>
      </div>
      <div class="mt-5 card-body">
        <h3>{{ $t('app.subscription.mass_change.view.change_date.title') }}</h3>

        {{  $filters.moment(mass_change.change_date, "LLL") }}
      </div>

      <div class="mt-5">
        <SubmitButton :in-progress="exportInProgress" @click="processExport">{{ $t('app.subscription.mass_change.view.export_button') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import fileDownload from "js-file-download";

export default {
  name: "MassChangeView",
  data() {
    return {
      ready: false,
      exportInProgress: false,
      mass_change: {},
      export_id: null,
    }
  },
  mounted() {

    var subscriptionId = this.$route.params.id
    axios.get('/app/subscription/mass-change/' + subscriptionId+'/view').then(response => {
      this.mass_change = response.data.mass_change;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
        this.errorMessage = this.$t('app.subscription.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.subscription.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  },
  methods: {
    checkDownloadExport: function () {

    },
    processExport: function () {
      this.exportInProgress = true;
      var subscriptionId = this.$route.params.id
      axios.get('/app/subscription/mass-change/' + subscriptionId+'/export', { responseType: 'blob' })
          .then((response) => {
            const contentType = response.headers['content-type'];

            if (contentType.includes('application/json')) {
              // Response is JSON
              return response.data.text();
            } else {
              const contentDisposition = response.headers['content-disposition'];
              var fileDownload = require('js-file-download');
              const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDisposition);
              if (matches !== null && matches[1]) {
                const fileName = matches[1].replace(/['"]/g, '');
                fileDownload(response.data, fileName);
                this.exportInProgress = false;
              }
            }
          })
          .then((responseData) => {
            if (responseData) {
              if (typeof responseData === 'string') {
                const jsonData = JSON.parse(responseData);
                console.log('Response is JSON:', jsonData);
              }
            }
          })
          .catch((error) => {
            console.error('An error occurred:', error);
          });
    }
  }
}
</script>

<style scoped>

</style>