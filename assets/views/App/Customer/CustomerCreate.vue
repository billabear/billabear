<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.create.title') }}</h1>
    <div class="mt-3 card-body">
      <form @submit.prevent="send">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.customer.create.email') }}
          </label>
          <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
          <input type="email" class="form-field-input" id="email" v-model="customer.email" />
          <p class="form-field-help">{{ $t('app.customer.create.help_info.email') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="country">
            {{ $t('app.customer.create.country') }}
          </label>
          <p class="form-field-error" v-if="errors.country != undefined">{{ errors.country }}</p>
          <input type="text" class="form-field-input" id="country"  v-model="customer.country"  />
          <p class="form-field-help">{{ $t('app.customer.create.help_info.country') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="reference">
            {{ $t('app.customer.create.reference') }}
          </label>
          <p class="form-field-error" v-if="errors.reference != undefined">{{ errors.reference }}</p>
          <input type="text" class="form-field-input" id="reference" v-model="customer.reference"  />
          <p class="form-field-help">{{ $t('app.customer.create.help_info.reference') }}</p>
        </div>

        <div class="form-field-ctn">
          <p @click="showAdvance = !showAdvance" class="cursor-pointer">
            <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
            <i class="fa-solid fa-caret-down" v-else></i>
            <span class="ml-2">{{ $t('app.customer.create.show_advanced') }}</span>
          </p>
        </div>

        <div class="form-field-ctn" v-if="showAdvance">
          <label class="form-field-lbl" for="email">
            {{ $t('app.customer.create.external_reference') }}
          </label>
          <p class="form-field-error" v-if="errors.external_reference != undefined">{{ errors.external_reference }}</p>
          <input type="text" class="form-field-input" id="external_reference" v-model="customer.external_reference"  />
          <p class="form-field-help">{{ $t('app.customer.create.help_info.external_reference') }}</p>
        </div>
        <div class="form-field-submit-ctn">
          <SubmitButton :in-progress="sendingInProgress">{{ $t('app.customer.create.submit_btn') }}</SubmitButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerCreate",
  data() {
    return {
      customer: {
        email: null,
        country: null,
        reference: null,
        external_reference: null,
      },
      sendingInProgress: false,
      showAdvance: false,
      errors: {

      }
    }
  },
  methods: {
    send: function () {
      this.sendingInProgress = true;
      axios.post('/app/customer', this.customer).then(
          response => {
            this.sendingInProgress = false;
          }
      ).catch(error => {
        this.errors = error.response.data.errors;
        this.sendingInProgress = false;
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