<template>
  <div v-if="!ready" class="text-center flex flex-wrap justify-content-center align-content-center ali h-full">
    <LoadingMessage>Loading</LoadingMessage>
  </div>
  <div v-else>
    <h3 class="text-xl font-weight-bold">{{ $t('app.billing.details.title') }}</h3>

    <form @submit.prevent="send" :disabled="sending">

      <label class="label">{{ $t('app.billing.details.street_line_one') }}</label>
      <input type="text" class="form-field" :class="{'form-error': errors.street_line_one !== undefined}" v-model="address.street_line_one" />
      <span class="error-message" v-if="errors.street_line_one" v-for="error in errors.street_line_one">{{ error }}</span>

      <label class="label">{{ $t('app.billing.details.street_line_two') }}</label>
      <input type="text" class="form-field" :class="{'form-error': errors.street_line_one !== undefined}" v-model="address.street_line_two" />
      <span class="error-message" v-if="errors.street_line_two" v-for="error in errors.street_line_two">{{ error }}</span>

      <label class="label">{{ $t('app.billing.details.city') }}</label>
      <input type="text" class="form-field" :class="{'form-error': errors.city !== undefined}" v-model="address.city" />
      <span class="error-message" v-if="errors.city" v-for="error in errors.city">{{ error }}</span>

      <label class="label">{{ $t('app.billing.details.region') }}</label>
      <input type="text" class="form-field" :class="{'form-error': errors.region !== undefined}" v-model="address.region" />
      <span class="error-message" v-if="errors.region" v-for="error in errors.region">{{ error }}</span>

      <label class="label">{{ $t('app.billing.details.country') }}</label>
      <input type="text" class="form-field" :class="{'form-error': errors.country !== undefined}" v-model="address.country" />
      <span class="error-message" v-if="errors.country" v-for="error in errors.country">{{ error }}</span>

      <label class="label">{{ $t('app.billing.details.postal_code') }}</label>
      <input type="text" class="form-field" :class="{'form-error': errors.postal_code !== undefined}" v-model="address.postal_code" />
      <span class="error-message" v-if="errors.postal_code" v-for="error in errors.postal_code">{{ error }}</span>

      <div class="mt-3">

        <SubmitButton :in-progress="sending">{{ $t('app.billing.details.submit') }}</SubmitButton>
      </div>
    </form>
  </div>

</template>

<script>


import {billingservice} from "../../../services/billingservice";

export default {
  name: "BillingAddress",
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
        postal_code: ''
      }
    }
  },
  methods: {
    send: function () {
        this.sending = true;
        billingservice.sendAddress(this.address).then(
            response => {
              this.sending = false;
            }
        )
    }
  },
  mounted() {
    billingservice.getAddress().then(
        address => {
          this.address = address.data.address;
          this.ready = true
        }
    )
  }
}
</script>

<style scoped>

</style>
