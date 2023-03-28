<template>
  <div>
    <h1 class="page-title">{{ $t('app.product.update.title') }}</h1>

    <form @submit.prevent="send">
    <div class="mt-3 card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.product.update.email') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field-input" id="name" v-model="product.name" />
          <p class="form-field-help">{{ $t('app.product.update.help_info.name') }}</p>
        </div>
    </div>

      <div class="form-field-ctn">
        <p @click="showAdvance = !showAdvance" class="cursor-pointer">
          <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
          <i class="fa-solid fa-caret-down" v-else></i>
          <span class="ml-2">{{ $t('app.product.update.show_advanced') }}</span>
        </p>
      </div>
    <div class="card-body mt-5" v-if="showAdvance">
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="email">
          {{ $t('app.product.update.external_reference') }}
        </label>
        <p class="form-field-error" v-if="errors.external_reference != undefined">{{ errors.external_reference }}</p>
        <input type="text" class="form-field-input" id="external_reference" v-model="product.external_reference"  />
        <p class="form-field-help">{{ $t('app.product.update.help_info.external_reference') }}</p>
      </div>

    </div>

    <div class="form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.product.update.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.product.update.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "productUpdate",
  data() {
    return {
      product: {
        name: null,
        external_reference: null,
      },
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      errors: {
      }
    }
  },
  mounted() {
    var productId = this.$route.params.id
    axios.get('/app/product/'+productId).then(response => {
      this.product = response.data.product;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
        this.errorMessage = this.$t('app.product.update.error.not_found')
      } else {
        this.errorMessage = this.$t('app.product.update.error.unknown')
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
      var productId = this.$route.params.id
      axios.post('/app/product/'+productId, this.product).then(
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