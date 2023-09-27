<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.product.update.title') }}</h1>

    <form @submit.prevent="send">
      <div class="p-5">
        <div class="card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.product.update.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field-input" id="name" v-model="product.name" />
          <p class="form-field-help">{{ $t('app.product.update.help_info.name') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="tax_rate">
            {{ $t('app.product.update.tax_rate') }}
          </label>
          <p class="form-field-error" v-if="errors.taxRate != undefined">{{ errors.taxRate }}</p>
          <input type="number" class="form-field-input" id="tax_rate" v-model="product.tax_rate" />
          <p class="form-field-help">{{ $t('app.product.update.help_info.tax_rate') }}</p>
        </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="tax_type">
              {{ $t('app.product.create.tax_type') }}
            </label>
            <p class="form-field-error" v-if="errors.taxType != undefined">{{ errors.taxType }}</p>
            <select class="form-field" id="name" v-model="product.tax_type">
              <option value="digital_goods">{{ $t('app.product.create.tax_types.digital_goods') }}</option>
              <option value="digital_services">{{ $t('app.product.create.tax_types.digital_services') }}</option>
              <option value="physical">{{ $t('app.product.create.tax_types.physical') }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.product.create.help_info.tax_type') }}</p>
          </div>
      </div>

        <div class="form-field-ctn mt-5">
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

      <div class="form-field-submit-ctn mt-5">
        <SubmitButton :in-progress="sendingInProgress">{{ $t('app.product.update.submit_btn') }}</SubmitButton>
      </div>
      <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.product.update.success_message') }}</p>
      </div>
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
</style>