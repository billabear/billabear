<template>
  <div>
    <h1>{{ $t('app.settings.stripe_import.main.title') }}</h1>

    <div class="my-5 text-end">
      <SubmitButton :in-progress="sendingRequest" @click="createImportRequest">{{ $t('app.settings.stripe_import.main.start_button') }}</SubmitButton>
    </div>

    <LoadingScreen :ready="ready">
      <div class="mt-3">
        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.settings.stripe_import.main.list.state') }}</th>
            <th>{{ $t('app.settings.stripe_import.main.list.last_id')}}</th>
            <th>{{ $t('app.settings.stripe_import.main.list.created_at') }}</th>
            <th>{{ $t('app.settings.stripe_import.main.list.updated_at') }}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="request in importRequests">
              <td>{{ request.state }}</td>
              <td>{{ request.last_id }}</td>
              <td>{{ request.created_at }}</td>
              <td>{{ request.updated_at }}</td>
            </tr>
            <tr v-if="importRequests.length === 0">
              <td colspan="4" class="text-center">{{ $t('app.settings.stripe_import.main.list.no_results') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "StripeImportList",
  data() {
    return {
      ready: false,
      sendingRequest: false,
      importRequests: [],
    }
  },
  methods: {
    createImportRequest: function () {
      this.sendingRequest = true;
      axios.post('/app/settings/stripe-import/start').then(response => {
        this.importRequests.push(response.data);
        this.sendingRequest = false;
      }).catch(error => {
        this.sendingRequest = false;
        if (error.response.status == 409) {
          alert(this.$t('app.settings.stripe_import.main.already_in_progress'))
        }
      })
    }
  },
  mounted() {
    axios.get('/app/settings/stripe-import').then(response => {

      this.importRequests = response.data.stripe_imports;
      this.ready = true;
    }).catch(error => {

    })
  }
}
</script>

<style scoped>

</style>