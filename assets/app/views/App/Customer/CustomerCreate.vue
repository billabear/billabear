<template>
  <div>
    <h1 class="page-title">{{ $t('app.customer.create.title') }}</h1>

    <form @submit.prevent="send">
    <div class="mt-3 card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.customer.create.email') }}
          </label>
          <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
          <input type="email" class="form-field-input" id="email" v-model="customer.email" />
          <p class="form-field-help">{{ $t('app.customer.create.help_info.email') }}</p>
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
        <label class="form-field-lbl" for="brand">
          {{ $t('app.customer.create.brand') }}
        </label>
        <p class="form-field-error" v-if="errors.brand != undefined">{{ errors.brand }}</p>
        <select class="form-field" id="brand" v-model="customer.brand">
          <option v-for="brand in brands" :value="brand.code">{{ brand.name }}</option>
        </select>
        <p class="form-field-help">{{ $t('app.customer.create.help_info.brand') }}</p>
      </div>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="locale">
          {{ $t('app.customer.create.locale') }}
        </label>
        <p class="form-field-error" v-if="errors.locale != undefined">{{ errors.locale }}</p>
        <input type="text" class="form-field-input" id="locale" v-model="customer.locale"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.locale') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="type">
          {{ $t('app.customer.create.type') }}
        </label>
        <p class="form-field-error" v-if="errors.type != undefined">{{ errors.type }}</p>
        <select class="form-field" id="type" v-model="customer.type">
          <option value="business">{{ $t('app.customer.create.type_business') }}</option>
          <option value="individual">{{ $t('app.customer.create.type_individual') }}</option>
        </select>
        <p class="form-field-help">{{ $t('app.customer.create.help_info.type') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="reference">
          {{ $t('app.customer.create.billing_type') }}
        </label>
        <p class="form-field-error" v-if="errors.billingType != undefined">{{ errors.billingType }}</p>
        <select class="form-field" id="reference" v-model="customer.billing_type">
          <option value="card">{{ $t('app.customer.create.billing_type_card') }}</option>
          <option value="invoice">{{ $t('app.customer.create.billing_type_invoice') }}</option>
        </select>
        <p class="form-field-help">{{ $t('app.customer.create.help_info.billing_type') }}</p>
      </div>
    </div>


      <div class="card-body mt-5">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="tax_number">
            {{ $t('app.customer.update.tax_number') }}
          </label>
          <p class="form-field-error" v-if="errors.taxNumber != undefined">{{ errors.taxNumber }}</p>
          <input type="text" class="form-field-input" id="tax_number" v-model="customer.tax_number"  />
          <p class="form-field-help">{{ $t('app.customer.update.help_info.tax_number') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="digital_tax_rate">
            {{ $t('app.customer.create.digital_tax_rate') }}
          </label>
          <p class="form-field-error" v-if="errors.digitalTaxRate != undefined">{{ errors.digitalTaxRate }}</p>
          <input type="number" class="form-field-input" id="digital_tax_rate" v-model="customer.digital_tax_rate"  />
          <p class="form-field-help">{{ $t('app.customer.create.help_info.digital_tax_rate') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="tax_number">
            {{ $t('app.customer.create.standard_tax_rate') }}
          </label>
          <p class="form-field-error" v-if="errors.standardTaxRate != undefined">{{ errors.standardTaxRate }}</p>
          <input type="number" class="form-field-input" id="standard_tax_rate" v-model="customer.standard_tax_rate"  />
          <p class="form-field-help">{{ $t('app.customer.create.help_info.standard_tax_rate') }}</p>
        </div>
      </div>

    <div class="card-body mt-5">
      <h2 class="mb-3">{{ $t('app.customer.create.address_title') }}</h2>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="street_line_one">
          {{ $t('app.customer.create.street_line_one') }}
        </label>
        <p class="form-field-error" v-if="errors['address.street_line_one'] != undefined">{{ errors['address.street_line_one'] }}</p>
        <input type="text" class="form-field-input" id="street_line_one"  v-model="customer.address.street_line_one"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.street_line_one') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="street_line_two">
          {{ $t('app.customer.create.street_line_two') }}
        </label>
        <p class="form-field-error" v-if="errors['address.street_line_two'] != undefined">{{ errors['address.street_line_two'] }}</p>
        <input type="text" class="form-field-input" id="street_line_two"  v-model="customer.address.street_line_two"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.street_line_two') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="city">
          {{ $t('app.customer.create.city') }}
        </label>
        <p class="form-field-error" v-if="errors['address.city'] != undefined">{{ errors['address.city'] }}</p>
        <input type="text" class="form-field-input" id="city"  v-model="customer.address.city"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.city') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="region">
          {{ $t('app.customer.create.region') }}
        </label>
        <p class="form-field-error" v-if="errors['address.region'] != undefined">{{ errors['address.region'] }}</p>
        <input type="text" class="form-field-input" id="region"  v-model="customer.address.region"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.region') }}</p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="country">
          {{ $t('app.customer.create.country') }}
        </label>
        <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
        <input type="text" class="form-field-input" id="country"  v-model="customer.address.country"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.country') }}</p>
      </div>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="post_code">
          {{ $t('app.customer.create.post_code') }}
        </label>
        <p class="form-field-error" v-if="errors['address.postcode'] != undefined">{{ errors['address.postcode'] }}</p>
        <input type="text" class="form-field-input" id="post_code"  v-model="customer.address.postcode"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.post_code') }}</p>
      </div>
    </div>

      <div class="form-field-ctn">
        <p @click="showAdvance = !showAdvance" class="cursor-pointer">
          <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
          <i class="fa-solid fa-caret-down" v-else></i>
          <span class="ml-2">{{ $t('app.customer.create.show_advanced') }}</span>
        </p>
      </div>
    <div class="card-body mt-5" v-if="showAdvance">
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="email">
          {{ $t('app.customer.create.external_reference') }}
        </label>
        <p class="form-field-error" v-if="errors.externalReference != undefined">{{ errors.externalReference }}</p>
        <input type="text" class="form-field-input" id="external_reference" v-model="customer.external_reference"  />
        <p class="form-field-help">{{ $t('app.customer.create.help_info.external_reference') }}</p>
      </div>

    </div>

    <div class="form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.customer.create.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.customer.create.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerCreate",
  data() {
    return {
      ready: false,
      brands: [],
      customer: {
        email: null,
        brand: 'default',
        address: {
          country: null,
        },
        reference: null,
        external_reference: null,
        tax_number: null,
        digital_tax_rate: null,
        standard_tax_rate: null,
      },
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      errors: {
      }
    }
  },
  mounted() {
    axios.get('/app/customer/create').then(response => {
      this.brands = response.data.brands;
    })
  },
  methods: {
    send: function () {
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      // Make sure empty strings aren't sent
      if (this.customer.digital_tax_rate == "") {
        this.customer.digital_tax_rate = null;
      }

      if (this.customer.standard_tax_rate == "") {
        this.customer.standard_tax_rate = null;
      }
      axios.post('/app/customer', this.customer).then(
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