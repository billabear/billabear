<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.vouchers.view.title') }}</h1>
    <LoadingScreen :ready="ready">
      <div class="p-5">
      <div class="card-body">
        <dl class="detail-list">
          <div>
            <dt>{{ $t('app.vouchers.view.main.name') }}</dt>
            <dd>{{ voucher.name }}</dd>
          </div>
          <div>
            <dt>{{ $t('app.vouchers.view.main.disabled') }}</dt>
            <dd>{{ voucher.disabled }}</dd>
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
      <RoleOnlyView role="ROLE_ACCOUNT_MANAGER">

        <div class="text-end mt-5">
          <SubmitButton :in-progress="disableProgess" class="btn--danger" v-if="!voucher.disabled" @click="disable">{{ $t('app.vouchers.view.disable') }}</SubmitButton>
          <SubmitButton :in-progress="enableProgress" class="btn--main" v-else @click="enable">{{ $t('app.vouchers.view.enable') }}</SubmitButton>
        </div>
      </RoleOnlyView></div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import RoleOnlyView from "../../../components/app/RoleOnlyView.vue";

export default {
  name: "VouchersView",
  components: {RoleOnlyView},
  data() {
    return {
      ready: false,
      voucher: {},
      amounts: [],
      disableProgress: false,
      enableProgress: false,
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/voucher/'+id).then(response => {
      this.voucher = response.data.voucher;
      this.amounts = response.data.amounts;
      this.ready = true;
    })
  },
  methods: {
    disable: function () {
      this.disableProgess = true;
      const id = this.$route.params.id;
      axios.post('/app/voucher/'+id+'/disable').then(response => {
        this.voucher.disabled = true;
        this.disableProgess = false;
      })
    },
    enable: function () {
      this.enableProgress = true;
      const id = this.$route.params.id;
      axios.post('/app/voucher/'+id+'/enable').then(response => {
        this.voucher.disabled = false;
        this.enableProgress = false;
      })
    }
  }
}
</script>

<style scoped>

</style>