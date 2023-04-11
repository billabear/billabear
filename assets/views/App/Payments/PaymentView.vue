<template>
  <div>
    <h1 class="page-title">{{ $t('app.payment.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="grid grid-cols-2 gap-3">
          <div class="mt-5">
            <h2 class="mb-3">{{ $t('app.payment.view.main.title') }}</h2>
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.payment.view.main.amount') }}</dt>
                <dd>{{ payment.amount }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.payment.view.main.currency') }}</dt>
                <dd>{{ payment.currency }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.payment.view.main.external_reference') }}</dt>
                <dd>
                  <a v-if="payment.payment_provider_details_url" target="_blank" :href="custpaymentomer.payment_provider_details_url">{{ payment.external_reference }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                  <span v-else>{{ payment.external_reference }}</span>
                </dd>
              </div>
            </dl>

          </div>
        </div>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>

  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "PaymentView",
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      payment: {},
    }
  },
  methods: {
  },
  mounted() {
    var paymentId = this.$route.params.id
    axios.get('/app/payments/'+paymentId).then(response => {
      this.payment = response.data.payment;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
          this.errorMessage = this.$t('app.payment.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.payment.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>