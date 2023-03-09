<template>
  <div>
    <h2 class="text-2xl mb-4">{{ $t('app.billing.payment_methods.title') }}</h2>

    <ul class="my-4">
      <li v-for="paymentDetail in paymentDetails">
        <div class="flex flex-row">
          <div class="mr-3">
            <label class="block font-weight-bold">{{ $t('app.billing.payment_methods.card_number') }}</label>
              **** **** **** {{ paymentDetail.last_four }}
          </div>
          <div class="">

            <label class="block font-weight-bold">{{ $t('app.billing.payment_methods.card_expiry') }}</label>
            {{ paymentDetail.expiry_month }}/{{ paymentDetail.expiry_year }}
          </div>
          <div class="w-100 text-right">
            <button class="btn--main" v-if="!paymentDetail.default">{{ $t('app.billing.payment_methods.make_default_btn') }}</button>
            <button class="btn--danger" @click="deleteCard({paymentDetail})">{{ $t('app.billing.payment_methods.delete_btn') }}</button>
          </div>
        </div>
      </li>
      <li v-if="paymentDetails.length === 0">
        <div class="text-center">
          {{ $t('app.billing.payment_methods.no_saved_payment_methods') }}
        </div>
      </li>
    </ul>
    <button class=" btn--main" @click="addCard" v-if="!show_add_card_form">
      {{ $t('app.billing.payment_methods.add_card_btn') }}
    </button>
    <StripeTokenForm v-if="show_add_card_form" />
  </div>
</template>

<script>
import StripeTokenForm from "./../../../components/app/Billing/Stripe/StripeTokenForm";
import {mapActions, mapState} from "vuex";
export default {
  name: "BillingMethods",
  components: {StripeTokenForm},
  data() {
    return {
    }
  },
  computed: {
    ...mapState('billingStore', ['show_add_card_form', 'paymentDetails'])
  },
  methods: {
    ...mapActions('billingStore', ['addCard', 'resetForm', 'fetchPaymentMethods', 'deleteCard']),
  },
  mounted() {
    this.resetForm();
    this.fetchPaymentMethods();
  }
}
</script>

<style scoped>

</style>
