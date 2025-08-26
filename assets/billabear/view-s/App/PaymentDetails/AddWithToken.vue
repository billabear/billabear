<template>
  <div>
    <h1 class="page-title">
      {{ $t('app.payment_details.add_with_token.title') }}
    </h1>

    <form @submit.prevent="send">
      <div class="card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="token">
            {{ $t('app.payment_details.add_with_token.field.token') }}
          </label>
          <p class="form-field-error" v-if="errors.token != undefined">
            {{ errors.token }}
          </p>
          <input
            type="text"
            class="form-field-input"
            id="token"
            v-model="token"
          />
          <p class="form-field-help">
            {{ $t('app.payment_details.add_with_token.help_info.token') }}
          </p>
        </div>
      </div>

      <div class="mt-5">
        <SubmitButton :in-progress="sending">{{
          $t('app.payment_details.add_with_token.submit')
        }}</SubmitButton>
      </div>
    </form>
  </div>
</template>

<script>
import { billingservice } from '../../../services/billingservice'

export default {
  name: 'AddWithToken',
  data() {
    return {
      token: '',
      errors: {},
      sending: false,
    }
  },
  methods: {
    send: function () {
      this.sending = true
      const customerId = this.$route.params.customerId
      billingservice.saveToken(customerId, this.token).then((response) => {
        const paymentDetails = response.data.payment_details
        this.sending = false
        this.$router.push({
          name: 'app.customer.view',
          params: { id: customerId },
        })
      })
    },
  },
}
</script>

<style scoped></style>
