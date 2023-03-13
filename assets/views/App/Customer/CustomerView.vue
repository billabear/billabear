<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.view.title') }}</h1>

    <div class="mt-3 card-body">
      <LoadingScreen :ready="ready">
        <div v-if="!error">
          <dl>
            <dt>{{ $t('app.customer.view.main.email') }}</dt>
            <dd>{{ customer.email }}</dd>
            <dt>{{ $t('app.customer.view.main.country') }}</dt>
            <dd>{{ customer.country }}</dd>
            <dt>{{ $t('app.customer.view.main.reference') }}</dt>
            <dd>{{ customer.reference }}</dd>
            <dt>{{ $t('app.customer.view.main.external_reference') }}</dt>
            <dd>{{ customer.external_reference }}</dd>
          </dl>

        </div>

        <div v-else>{{ errorMessage }}</div>
      </LoadingScreen>
    </div>

  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerView",
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      customer: {
      }
    }
  },
  mounted() {
    var customerId = this.$route.params.id
    axios.get('/app/customer/'+customerId).then(response => {
      this.customer = response.data.customer;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
          this.errorMessage = this.$t('app.customer.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.customer.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>