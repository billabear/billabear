<template>
  <div>
    <h1 class="page-title">{{ $t('app.product.create.title') }}</h1>
    <LoadingScreen :ready="ready">

      <form @submit.prevent="send">
        <div class="m-5 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.product.create.name') }}
            </label>
            <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
            <input type="text" class="form-field-input" id="name" v-model="product.name" />
            <p class="form-field-help">{{ $t('app.product.create.help_info.name') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="tax_rate">
              {{ $t('app.product.create.tax_rate') }}
            </label>
            <p class="form-field-error" v-if="errors.taxRate != undefined">{{ errors.taxRate }}</p>
            <input type="number" class="form-field-input" id="tax_rate" v-model="product.tax_rate" />
            <p class="form-field-help">{{ $t('app.product.create.help_info.tax_rate') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="tax_type">
              {{ $t('app.product.create.tax_type') }}
            </label>
            <p class="form-field-error" v-if="errors.taxType != undefined">{{ errors.taxType }}</p>
            <select class="form-field" id="name" v-model="product.tax_type">
              <option v-for="tax_type in tax_types" v-bind:value="tax_type.id">{{ tax_type.name }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.product.create.help_info.tax_type') }}</p>
          </div>
        </div>

        <div class="form-field-ctn ml-5">
          <p @click="showAdvance = !showAdvance" class="cursor-pointer">
            <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
            <i class="fa-solid fa-caret-down" v-else></i>
            <span class="ml-2">{{ $t('app.product.create.show_advanced') }}</span>
          </p>
        </div>
        <div class="card-body mt-5 ml-5" v-if="showAdvance">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="email">
              {{ $t('app.product.create.external_reference') }}
            </label>
            <p class="form-field-error" v-if="errors.external_reference != undefined">{{ errors.external_reference }}</p>
            <input type="text" class="form-field-input" id="external_reference" v-model="product.external_reference"  />
            <p class="form-field-help">{{ $t('app.product.create.help_info.external_reference') }}</p>
          </div>

        </div>

        <div class="form-field-submit-ctn ml-5">
          <SubmitButton :in-progress="sendingInProgress">{{ $t('app.product.create.submit_btn') }}</SubmitButton>
        </div>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.product.create.success_message') }}</p>
      </form>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "productCreate",
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
      },
      ready: false,
      tax_types: []
    }
  },
  mounted() {
    axios.get('/app/product/create').then(response => {
      this.ready = true;
      this.tax_types = response.data.tax_types;
    })
  },
  methods: {
    send: function () {
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      axios.post('/app/product', this.product).then(
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