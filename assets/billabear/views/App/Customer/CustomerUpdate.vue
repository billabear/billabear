<template>
  <div>
    <h1 class="mt-5 ml-5 page-title">{{ $t('app.customer.update.title') }}</h1>

    <LoadingScreen :ready="ready">

      <form @submit.prevent="send">
        <div class="">
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
              <label class="form-field-lbl" for="type">
                {{ $t('app.customer.update.type') }}
              </label>
              <p class="form-field-error" v-if="errors.type != undefined">{{ errors.type }}</p>
              <select class="form-field" id="type" v-model="customer.type">
                <option value="business">{{ $t('app.customer.update.type_business') }}</option>
                <option value="individual">{{ $t('app.customer.update.type_individual') }}</option>
              </select>
              <p class="form-field-help">{{ $t('app.customer.update.help_info.type') }}</p>
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
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="type">
                {{ $t('app.customer.update.marketing_opt_in') }}
              </label>
              <p class="form-field-error" v-if="errors.marketingOptIn != undefined">{{ errors.marketingOptIn }}</p>
              <Toggle v-model="customer.marketing_opt_in" />
              <p class="form-field-help">{{ $t('app.customer.update.help_info.marketing_opt_in') }}</p>
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
              <label class="form-field-lbl" for="tax_number">
                {{ $t('app.customer.update.standard_tax_rate') }}
              </label>
              <p class="form-field-error" v-if="errors.standardTaxRate != undefined">{{ errors.standardTaxRate }}</p>
              <input type="number" class="form-field-input" id="standard_tax_rate" v-model="customer.standard_tax_rate"  />
              <p class="form-field-help">{{ $t('app.customer.update.help_info.standard_tax_rate') }}</p>
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="tax_number">
                {{ $t('app.customer.update.invoice_format') }}
              </label>
              <p class="form-field-error" v-if="errors.invoiceFormat != undefined">{{ errors.invoiceFormat }}</p>
              <select class="form-field" v-model="customer.invoice_format">
                <option value="pdf">PDF</option>
                <option value="zugferd_v1"> ZUGFeRD v1 - Factur-X/XRechung</option>
              </select>
              <p class="form-field-help">{{ $t('app.customer.update.help_info.invoice_format') }}</p>
            </div>
          </div>


          <div class="card-body mt-5">
            <h2 class="mb-3">{{ $t('app.customer.update.address_title') }}</h2>
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="street_line_one">
                {{ $t('app.customer.update.company_name') }}
              </label>
              <p class="form-field-error" v-if="errors['address.company_name'] != undefined">{{ errors['address.company_name'] }}</p>
              <input type="text" class="form-field-input" id="street_line_one"  v-model="customer.address.company_name"  />
              <p class="form-field-help">{{ $t('app.customer.update.help_info.company_name') }}</p>
            </div>
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
              <p class="form-field-error" v-if="errors['address.postcode'] != undefined">{{ errors['address.postcode'] }}</p>
              <input type="text" class="form-field-input" id="post_code"  v-model="customer.address.postcode"  />
              <p class="form-field-help">{{ $t('app.customer.update.help_info.post_code') }}</p>
            </div>
          </div>

          <div class="card-body mt-5">
            <h2 class="mb-3">{{ $t('app.customer.update.metadata.title') }}</h2>

            <table class="w-1/2">
              <thead>
              <tr>
                <th class="text-left">{{ $t('app.customer.update.metadata.name') }}</th>
                <th class="text-left">{{ $t('app.customer.update.metadata.value') }}</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(metaValue, key) in metadata">
                <td><input type="text" class="form-field" v-model="metaValue.key"></td>
                <td><input type="text" class="form-field" v-model="metaValue.value"></td>
                <td><button class="btn--danger" @click="removeMetadata(key)"><i class="fa-solid fa-trash"></i></button></td>
              </tr>
              <tr v-if="metadata.length === 0">
                <td colspan="4" class="text-center">{{ $t('app.customer.update.metadata.no_values') }}</td>
              </tr>
              </tbody>
            </table>
            <button class="btn--main mt-3" @click="addMetadata">
              <i class="fa-solid fa-plus"></i>
              {{ $t('app.customer.update.metadata.add') }}
            </button>
          </div>

          <div class="form-field-ctn mt-3">
            <p @click="showAdvance = !showAdvance" class="cursor-pointer">
              <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
              <i class="fa-solid fa-caret-down" v-else></i>
              <span class="ml-2">{{ $t('app.customer.update.show_advanced') }}</span>
            </p>
          </div>
          <div class="card-body mt-3" v-if="showAdvance">
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="email">
                {{ $t('app.customer.update.external_reference') }}
              </label>
              <p class="form-field-error" v-if="errors.external_reference != undefined">{{ errors.external_reference }}</p>
              <input type="text" class="form-field-input" id="external_reference" v-model="customer.external_reference"  />
              <p class="form-field-help">{{ $t('app.customer.update.help_info.external_reference') }}</p>
            </div>

          </div>

          <div class="form-field-submit-ctn mt-3">
            <SubmitButton :in-progress="sendingInProgress">{{ $t('app.customer.update.submit_btn') }}</SubmitButton>
          </div>
          <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.customer.update.success_message') }}</p>
        </div>
      </form>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import {Button, Toggle} from "flowbite-vue";

export default {
  name: "CustomerUpdate",
  components: {Button, Toggle},
  data() {
    return {
      customer: {
        email: null,
        address: {
          country: null,
        },
        reference: null,
        external_reference: null,
        tax_number: null,
        digital_tax_rate: null,
        standard_tax_rate: null,
      },
      metadata: [],
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
      for (let key in this.customer.metadata) {
        this.metadata.push({key: key, value: this.customer.metadata[key]});
      }

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
    addMetadata: function () {
      this.metadata.push({key: '', value: ''});
    },
    removeMetadata: function (key) {
      this.metadata.splice(key, 1)
    },
    send: function () {
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      var customerId = this.$route.params.id

      // Make sure empty strings aren't sent
      if (this.customer.digital_tax_rate == "") {
        this.customer.digital_tax_rate = null;
      }

      if (this.customer.standard_tax_rate == "") {
        this.customer.standard_tax_rate = null;
      }
      const payload = this.customer;
      let metadata = {};
      for (let i = 0; i < this.metadata.length; i++) {
        metadata[this.metadata[i].key] = this.metadata[i].value;
      }
      payload.metadata = metadata;

      axios.post('/app/customer/'+customerId, payload).then(
          response => {
            this.sendingInProgress = false;
            this.success = true;
            this.$router.push({name: 'app.customer.view', params: {id: customerId}});
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
