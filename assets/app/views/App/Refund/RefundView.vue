<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.refund.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div v-if="!error">
        <div class="grid grid-cols-2 gap-3 p-5">
          <div class="card-body">
            <h2 class="section-header">{{ $t('app.refund.view.main.title') }}</h2>
            <div class="section-body">
              <dl class="detail-list">
                <div>
                  <dt>{{ $t('app.refund.view.main.amount') }}</dt>
                  <dd>{{ currency(refund.amount) }}</dd>
                </div>
                <div>
                  <dt>{{ $t('app.refund.view.main.currency') }}</dt>
                  <dd>{{ refund.currency }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div v-else>{{ errorMessage }}</div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import {VueFinalModal} from "vue-final-modal";
import currency from "currency.js";

export default {
  name: "PaymentView",
  components: {VueFinalModal},
  data() {
    return {
      ready: false,
      error: false,
      errorMessage: null,
      payment: {},
      refund: {},
      subscriptions: [],
      refundValues: {
        errors: {},
        refundValue: 0,
        reason: null,
        inProgress: false,
      },
      options: {
        teleportTo: 'body',
        modelValue: false,
        displayDirective: 'if',
        hideOverlay: false,
        overlayTransition: 'vfm-fade',
        contentTransition: 'vfm-fade',
        clickToClose: true,
        escToClose: true,
        background: 'non-interactive',
        lockScroll: true,
        swipeToClose: 'none',
      },
    }
  },
  mounted() {
    var refund = this.$route.params.id
    axios.get('/app/refund/'+refund).then(response => {
      this.refund = response.data.refund;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
          this.errorMessage = this.$t('app.refund.view.error.not_found')
      } else {
        this.errorMessage = this.$t('app.refund.view.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  },
  methods: {
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
  }
}
</script>

<style scoped>

</style>