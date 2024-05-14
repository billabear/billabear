<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.subscription.mass_change.view.title') }}</h1>
    <LoadingScreen :ready="ready">
      <div class="p-5">
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

        <div class="mt-5 card-body" v-if="estimate !== null">
          {{ $t('app.subscription.mass_change.view.estimate.amount', {amount: currency(this.estimate.amount), currency: this.estimate.currency, schedule: this.estimate.schedule}) }}
        </div>
        <div class="mt-5">
          <SubmitButton :in-progress="exportInProgress" @click="processExport">{{ $t('app.subscription.mass_change.view.export_button') }}</SubmitButton>
          <SubmitButton :in-progress="cancelInProgress" @click="cancel" class="ml-5 btn--danger" v-if="mass_change.status == 'created'">{{ $t('app.subscription.mass_change.view.cancel') }}</SubmitButton>
          <SubmitButton :in-progress="cancelInProgress" @click="uncancel" class="ml-5 btn--secondary" v-if="mass_change.status == 'cancelled'">{{ $t('app.subscription.mass_change.view.uncancel') }}</SubmitButton>
        </div>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import fileDownload from "js-file-download";
import currency from "currency.js";

export default {
  name: "MassChangeView",
  data() {
    return {
      ready: false,
      exportInProgress: false,
      cancelInProgress: false,
      mass_change: {},
      export_id: null,
      estimate: {},
    }
  },
  mounted() {

    var subscriptionId = this.$route.params.id
    axios.get('/app/subscription/mass-change/' + subscriptionId+'/view').then(response => {
      this.mass_change = response.data.mass_change;
      this.estimate = response.data.estimate;
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
    cancel: function () {
      this.cancelInProgress = true;
      var subscriptionId = this.$route.params.id
      axios.post('/app/subscription/mass-change/' + subscriptionId+'/cancel').then(response => {
        this.cancelInProgress = false;
        this.mass_change.status = response.data.status;
      })
    },
    uncancel: function () {
      this.cancelInProgress = true;
      var subscriptionId = this.$route.params.id
      axios.post('/app/subscription/mass-change/' + subscriptionId+'/uncancel').then(response => {
        this.cancelInProgress = false;
        this.mass_change.status = response.data.status;
      })
    },
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
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