<template>

  <div>
    <h1 class="page-title">{{ $t('app.settings.brand_settings.create.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="m-5">
      <form @submit.prevent="save">
        <div class="mt-3 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="reference">
              {{ $t('app.settings.brand_settings.create.fields.name') }}
            </label>
            <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
            <input type="text" class="form-field-input" id="reference" v-model="brand.name"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.name') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="reference">
              {{ $t('app.settings.brand_settings.create.fields.code') }}
            </label>
            <input type="text" class="form-field-input" id="reference" v-model="brand.code" />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.code') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="email">
              {{ $t('app.settings.brand_settings.create.fields.email') }}
            </label>
            <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
            <input type="email" class="form-field-input" id="email" v-model="brand.email_address" />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.email') }}</p>
          </div>

        </div>


        <div class="card-body mt-5">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="tax_number">
              {{ $t('app.settings.brand_settings.create.fields.tax_number') }}
            </label>
            <input type="text" class="form-field-input" id="tax_number" v-model="brand.tax_number" />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.tax_number') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="tax_rate">
              {{ $t('app.settings.brand_settings.create.fields.tax_rate') }}
            </label>
            <input type="text" class="form-field-input" id="tax_rate" v-model="brand.tax_rate" />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.tax_rate') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="tax_rate">
              {{ $t('app.settings.brand_settings.create.fields.digital_services_tax_rate') }}
            </label>
            <p class="form-field-error" v-if="errors.digitalServicesTaxRate != undefined">{{ errors.digitalServicesTaxRate }}</p>
            <input type="text" class="form-field-input" id="tax_rate" v-model="brand.digital_services_tax_rate" />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.digital_services_tax_rate') }}</p>
          </div>
        </div>


        <div class="card-body mt-5">
          <h2 class="mb-3">{{ $t('app.settings.brand_settings.create.address_title') }}</h2>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="company_name">
              {{ $t('app.settings.brand_settings.create.fields.company_name') }}
            </label>
            <p class="form-field-error" v-if="errors['address.companyName'] != undefined">{{ errors['address.companyName'] }}</p>
            <input type="text" class="form-field-input" id="company_name"  v-model="brand.address.company_name"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.street_line_one') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="street_line_one">
              {{ $t('app.settings.brand_settings.update.fields.street_line_one') }}
            </label>
            <p class="form-field-error" v-if="errors['address.streetLineOne'] != undefined">{{ errors['address.streetLineOne'] }}</p>
            <input type="text" class="form-field-input" id="street_line_one"  v-model="brand.address.street_line_one"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.street_line_one') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="street_line_two">
              {{ $t('app.settings.brand_settings.update.fields.street_line_two') }}
            </label>
            <p class="form-field-error" v-if="errors['address.streetLineTwo'] != undefined">{{ errors['address.streetLineTwo'] }}</p>
            <input type="text" class="form-field-input" id="street_line_two"  v-model="brand.address.street_line_two"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.street_line_two') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="city">
              {{ $t('app.settings.brand_settings.update.fields.city') }}
            </label>
            <p class="form-field-error" v-if="errors['address.city'] != undefined">{{ errors['address.city']  }}</p>
            <input type="text" class="form-field-input" id="city"  v-model="brand.address.city"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.city') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="region">
              {{ $t('app.settings.brand_settings.update.fields.region') }}
            </label>
            <p class="form-field-error" v-if="errors['address.region'] != undefined">{{ errors['address.region'] }}</p>
            <input type="text" class="form-field-input" id="region"  v-model="brand.address.region"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.region') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="country">
              {{ $t('app.settings.brand_settings.update.fields.country') }}
            </label>
            <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
            <input type="text" class="form-field-input" id="country"  v-model="brand.address.country"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.country') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="post_code">
              {{ $t('app.settings.brand_settings.create.fields.postcode') }}
            </label>
            <p class="form-field-error" v-if="errors['address.postcode'] != undefined">{{ errors['address.postcode'] }}</p>
            <input type="text" class="form-field-input" id="post_code"  v-model="brand.address.postcode"  />
            <p class="form-field-help">{{ $t('app.settings.brand_settings.create.help_info.postcode') }}</p>
          </div>
        </div>

        <div class="mt-5 form-field-submit-ctn">
          <SubmitButton :in-progress="sending">{{ $t('app.settings.brand_settings.create.submit_btn') }}</SubmitButton>
        </div>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.brand_settings.create.success_message') }}</p>
      </form></div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "BrandSettingUpdate",
  data() {
    return {
      ready: true,
      brand: {address: {}},
      errors: {},
      sending: false,
    }
  },
  mounted() {
  },
  methods: {
    save: function () {
        this.sending = true;
        const payload = {
          code: this.brand.code,
          name: this.brand.name,
          email_address: this.brand.email_address,
          address: {
            company_name: this.brand.address.company_name,
            street_line_one: this.brand.address.street_line_one,
            street_line_two: this.brand.address.street_line_two,
            region: this.brand.address.region,
            city: this.brand.address.city,
            country: this.brand.address.country,
            postcode: this.brand.address.postcode,
          },
          tax_number: this.brand.tax_number,
          tax_rate: this.brand.tax_rate != "" ? this.brand.tax_rate : null,
          digital_services_tax_rate: this.brand.digital_services_tax_rate != "" ? this.brand.digital_services_tax_rate : null,
        };

        axios.post('/app/settings/brand', payload).then(response => {
          this.sending = false;
        }).catch(error => {
          if (error.response.status == 404) {
            this.errorMessage = this.$t('app.settings.brand_settings.update.error.not_found')
          } else {
            this.errorMessage = this.$t('app.settings.brand_settings.update.error.unknown')
          }
          this.errors = error.response.data.errors;
          this.sending = false;
        })
    }
  }
}
</script>

<style scoped>

</style>