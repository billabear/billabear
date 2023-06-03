<template>
  <div>
    <h1>{{ $t('app.vouchers.view.title') }}</h1>
    <LoadingScreen :ready="ready">
      <div class="mt-5">
        <dl class="detail-list">
          <div>
            <dt>{{ $t('app.vouchers.view.main.name') }}</dt>
            <dd>{{ voucher.name }}</dd>
          </div>
          <div>
            <dt>{{ $t('app.vouchers.view.main.type') }}</dt>
            <dd>{{ voucher.type }}</dd>
          </div>
          <div v-if="voucher.type === 'fixed_credit'" v-for="amount in amounts">
            <dt>{{ $t('app.vouchers.view.main.amount', {currency: amount.currency}) }}</dt>
            <dd>{{ amount.amount }}</dd>
          </div>
          <div v-if="voucher.type === 'percentage'">
            <dt>{{ $t('app.vouchers.view.main.percentage') }}</dt>
            <dd>{{ voucher.percentage }}</dd>
          </div>
          <div>
            <dt>{{ $t('app.vouchers.view.main.entry_type') }}</dt>
            <dd>{{ voucher.entry_type }}</dd>
          </div>
          <div v-if="voucher.entry_type === 'manual'">
            <dt>{{ $t('app.vouchers.view.main.code') }}</dt>
            <dd>{{ voucher.code }}</dd>
          </div>
          <div v-if="voucher.entry_type === 'automatic'">

            <dt>{{ $t('app.vouchers.view.main.automatic_event') }}</dt>
            <dd>{{ voucher.automatic_event }}</dd>
          </div>
        </dl>

      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "VouchersView",
  data() {
    return {
      ready: false,
      voucher: {},
      amounts: []
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/voucher/'+id).then(response => {
      this.voucher = response.data.voucher;
      this.amounts = response.data.amounts;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>