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
          <div v-if="voucher.type === 'fixed_credit'">
          
          </div>
          <div>
            <dt>{{ $t('app.vouchers.view.main.entry_type') }}</dt>
            <dd>{{ voucher.entry_type }}</dd>
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
      voucher: {}
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/voucher/'+id).then(response => {
      this.voucher = response.data.voucher;
      this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>