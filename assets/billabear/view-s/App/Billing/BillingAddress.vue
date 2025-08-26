<template>
  <div
    v-if="!ready"
    class="text-center flex flex-wrap justify-content-center align-content-center ali h-full"
  >
    <LoadingMessage>Loading</LoadingMessage>
  </div>
  <div v-else>
    <h3 class="text-xl font-weight-bold">
      {{ $t('app.billing.details.title') }}
    </h3>

    <form @submit.prevent="send" :disabled="sending">
      <label class="label">{{
        $t('app.billing.details.street_line_one')
      }}</label>
      <input
        type="text"
        class="form-field"
        :class="{ 'form-error': errors.street_line_one !== undefined }"
        v-model="address.street_line_one"
      />
      <template v-for="error in errors.street_line_one">
        <span v-if="errors.street_line_one" class="error-message">{{
          error
        }}</span>
      </template>

      <label class="label">{{
        $t('app.billing.details.street_line_two')
      }}</label>
      <input
        type="text"
        class="form-field"
        :class="{ 'form-error': errors.street_line_one !== undefined }"
        v-model="address.street_line_two"
      />
      <template v-for="error in errors.street_line_two">
        <span v-if="errors.street_line_two" class="error-message">{{
          error
        }}</span>
      </template>

      <label class="label">{{ $t('app.billing.details.city') }}</label>
      <input
        type="text"
        class="form-field"
        :class="{ 'form-error': errors.city !== undefined }"
        v-model="address.city"
      />
      <template v-for="error in errors.city">
        <span v-if="errors.city" class="error-message">{{ error }}</span>
      </template>

      <label class="label">{{ $t('app.billing.details.region') }}</label>
      <input
        type="text"
        class="form-field"
        :class="{ 'form-error': errors.region !== undefined }"
        v-model="address.region"
      />
      <template v-for="error in errors.region">
        <span v-if="errors.region" class="error-message">{{ error }}</span>
      </template>

      <label class="label">{{ $t('app.billing.details.country') }}</label>
      <input
        type="text"
        class="form-field"
        :class="{ 'form-error': errors.country !== undefined }"
        v-model="address.country"
      />
      <template v-for="error in errors.country">
        <span v-if="errors.country" class="error-message">{{ error }}</span>
      </template>

      <label class="label">{{ $t('app.billing.details.postal_code') }}</label>
      <input
        type="text"
        class="form-field"
        :class="{ 'form-error': errors.postal_code !== undefined }"
        v-model="address.postal_code"
      />
      <template v-for="error in errors.postal_code">
        <span v-if="errors.postal_code" class="error-message">{{ error }}</span>
      </template>

      <div class="mt-3">
        <SubmitButton :in-progress="sending">{{
          $t('app.billing.details.submit')
        }}</SubmitButton>
      </div>
    </form>
  </div>
</template>

<script>
import { billingservice } from '../../../services/billingservice'

export default {
  name: 'BillingAddress',
  data() {
    return {
      ready: false,
      sending: false,
      errors: {},
      address: {
        street_line_one: '',
        street_line_two: '',
        city: '',
        region: '',
        country: '',
        postal_code: '',
      },
    }
  },
  methods: {
    send: function () {
      this.sending = true
      billingservice.sendAddress(this.address).then((response) => {
        this.sending = false
      })
    },
  },
  mounted() {
    billingservice.getAddress().then((address) => {
      this.address = address.data.address
      this.ready = true
    })
  },
}
</script>

<style scoped></style>
