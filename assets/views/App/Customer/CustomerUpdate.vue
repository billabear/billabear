<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.update.title') }}</h1>

    <form @submit.prevent="send">
    <div class="mt-3 card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.customer.update.email') }}
          </label>
          <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
          <input type="email" class="form-field-input" id="email" v-model="customer.email" />
          <p class="form-field-help">{{ $t('app.customer.update.help_info.email') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="locale">
            {{ $t('app.customer.update.locale') }}
          </label>
          <p class="form-field-error" v-if="errors.locale != undefined">{{ errors.locale }}</p>
          <input type="text" class="form-field-input" id="locale" v-model="customer.locale" />
          <p class="form-field-help">{{ $t('app.customer.update.help_info.locale') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="reference">
            {{ $t('app.customer.update.reference') }}
          </label>
          <p class="form-field-error" v-if="errors.reference != undefined">{{ errors.reference }}</p>
          <input type="text" class="form-field-input" id="reference" v-model="customer.reference"  />
          <p class="form-field-help">{{ $t('app.customer.update.help_info.reference') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="reference">
            {{ $t('app.customer.update.billing_type') }}
          </label>
          <p class="form-field-error" v-if="errors.billingType != undefined">{{ errors.billingType }}</p>
          <select class="form-field" id="reference" v-model="customer.billing_type">
            <option value="card">{{ $t('app.customer.update.billing_type_card') }}</option>
            <option value="invoice">{{ $t('app.customer.update.billing_type_invoice') }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.customer.update.help_info.billing_type') }}</p>
        </div>
    </div>

    <div class="card-body mt-5">
      <h2 class="mb-3">{{ $t('app.customer.update.address_title') }}</h2>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="street_line_one">
          {{ $t('app.customer.update.street_line_one') }}
        </label>
        <p class="form-field-error" v-if="errors['address.street_line_one'] != undefined">{{ errors['address.street_line_one'] }}</p>
        <input type="text" class="form-field-input" id="street_line_one"  v-model="customer.address.street_line_one"  />
        <p class="form-field-help">{{ $t('app.customer.update.help_info.street_line_one') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="street_line_two">
          {{ $t('app.customer.update.street_line_two') }}
        </label>
        <p class="form-field-error" v-if="errors['address.street_line_two'] != undefined">{{ errors['address.street_line_two'] }}</p>
        <input type="text" class="form-field-input" id="street_line_two"  v-model="customer.address.street_line_two"  />
        <p class="form-field-help">{{ $t('app.customer.update.help_info.street_line_two') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="city">
          {{ $t('app.customer.update.city') }}
        </label>
        <p class="form-field-error" v-if="errors['address.city'] != undefined">{{ errors['address.city'] }}</p>
        <input type="text" class="form-field-input" id="city"  v-model="customer.address.city"  />
        <p class="form-field-help">{{ $t('app.customer.update.help_info.city') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="region">
          {{ $t('app.customer.update.region') }}
        </label>
        <p class="form-field-error" v-if="errors['address.region'] != undefined">{{ errors['address.region'] }}</p>
        <input type="text" class="form-field-input" id="region"  v-model="customer.address.region"  />
        <p class="form-field-help">{{ $t('app.customer.update.help_info.region') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="country">
          {{ $t('app.customer.update.country') }}
        </label>
        <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
        <input type="text" class="form-field-input" id="country"  v-model="customer.address.country"  />
        <p class="form-field-help">{{ $t('app.customer.update.help_info.country') }}</p>
      </div>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="post_code">
          {{ $t('app.customer.update.post_code') }}
        </label>
        <p class="form-field-error" v-if="errors['address.post_code'] != undefined">{{ errors['address.post_code'] }}</p>
        <input type="text" class="form-field-input" id="post_code"  v-model="customer.address.post_code"  />
        <p class="form-field-help">{{ $t('app.customer.update.help_info.post_code') }}</p>
      </div>
    </div>

      <div class="form-field-ctn">
        <p @click="showAdvance = !showAdvance" class="cursor-pointer">
          <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
          <i class="fa-solid fa-caret-down" v-else></i>
          <span class="ml-2">{{ $t('app.customer.update.show_advanced') }}</span>
        </p>
      </div>
    <div class="card-body mt-5" v-if="showAdvance">
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="email">
          {{ $t('app.customer.update.external_reference') }}
        </label>
        <p class="form-field-error" v-if="errors.external_reference != undefined">{{ errors.external_reference }}</p>
        <input type="text" class="form-field-input" id="external_reference" v-model="customer.external_reference"  />
        <p class="form-field-help">{{ $t('app.customer.update.help_info.external_reference') }}</p>
      </div>

    </div>

    <div class="form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.customer.update.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.customer.update.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerUpdate",
  data() {
    return {
      customer: {
        email: null,
        address: {
          country: null,
        },
        reference: null,
        external_reference: null,
      },
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      ready: false,
      errors: {
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
        this.errorMessage = this.$t('app.customer.update.error.not_found')
      } else {
        this.errorMessage = this.$t('app.customer.update.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  },
  methods: {
    send: function () {
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      var customerId = this.$route.params.id
      axios.post('/app/customer/'+customerId, this.customer).then(
          response => {
            this.sendingInProgress = false;
            this.success = true;
          }
      ).catch(error => {
        this.errors = error.response.data.errors;
        this.sendingInProgress = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>
.form-field-error {
  @apply text-red-500 text-xs italic mb-2;
}

.form-field-ctn {
  @apply w-full md:w-1/2 px-3 mb-6 md:mb-0 pt-2;
}

.form-field-lbl {
  @apply block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2;
}

.form-field-input {
  @apply appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white;
}

.form-field-help {
  @apply text-gray-600 text-xs italic;
}

.form-field-submit-ctn {
  @apply mt-3;
}
</style>