<template>

  <div>
    <h1 class="page-title">{{ $t('app.settings.brand_settings.update.title', {name: brand.name}) }}</h1>

    <LoadingScreen :ready="ready">
      <div class="m-5">
      <form @submit.prevent="save">
        <div class="border-b pb-2 my-3 border-black">
          <a @click="view = 'general'" class="viewLink" :class="{activeView: view === 'general'}">{{ $t('app.settings.brand_settings.update.general') }}</a> |
          <a @click="view = 'notifications'" class="viewLink" :class="{activeView: view === 'notifications'}">{{ $t('app.settings.brand_settings.update.notifications') }}</a>
        </div>
        <div v-if="view === 'general'">
          <div class="card-body mt-3">
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="reference">
                {{ $t('app.settings.brand_settings.update.fields.name') }}
              </label>
              <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
              <input type="text" class="form-field-input" id="reference" v-model="brand.name"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.name') }}</p>
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="reference">
                {{ $t('app.settings.brand_settings.update.fields.code') }}
              </label>
              <input type="text" class="form-field-input" id="reference" v-model="brand.code" disabled />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.code') }}</p>
            </div>


            <div class="form-field-ctn">
              <label class="form-field-lbl" for="email">
                {{ $t('app.settings.brand_settings.update.fields.email') }}
              </label>
              <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
              <input type="email" class="form-field-input" id="email" v-model="brand.email_address" />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.email') }}</p>
            </div>

          </div>

          <div class="card-body mt-5">
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="tax_number">
                {{ $t('app.settings.brand_settings.update.fields.tax_number') }}
              </label>
              <p class="form-field-error" v-if="errors.taxNumber != undefined">{{ errors.taxNumber }}</p>
              <input type="text" class="form-field-input" id="tax_number" v-model="brand.tax_number" />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.tax_number') }}</p>
            </div>
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="tax_rate">
                {{ $t('app.settings.brand_settings.update.fields.tax_rate') }}
              </label>
              <p class="form-field-error" v-if="errors.taxRate != undefined">{{ errors.taxRate }}</p>
              <input type="text" class="form-field-input" id="tax_rate" v-model="brand.tax_rate" />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.tax_rate') }}</p>
            </div>
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="tax_rate">
                {{ $t('app.settings.brand_settings.update.fields.digital_services_tax_rate') }}
              </label>
              <p class="form-field-error" v-if="errors.digitalServicesTaxRate != undefined">{{ errors.digitalServicesTaxRate }}</p>
              <input type="text" class="form-field-input" id="tax_rate" v-model="brand.digital_services_tax_rate" />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.digital_services_tax_rate') }}</p>
            </div>
          </div>

          <div class="card-body mt-5">
            <h2 class="mb-3">{{ $t('app.settings.brand_settings.update.address_title') }}</h2>
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="company_name">
                {{ $t('app.settings.brand_settings.update.fields.company_name') }}
              </label>
              <p class="form-field-error" v-if="errors['address.companyName'] != undefined">{{ errors['address.companyName'] }}</p>
              <input type="text" class="form-field-input" id="company_name"  v-model="brand.address.company_name"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.street_line_one') }}</p>
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="street_line_one">
                {{ $t('app.settings.brand_settings.update.fields.street_line_one') }}
              </label>
              <p class="form-field-error" v-if="errors['address.streetLineOne'] != undefined">{{ errors['address.streetLineOne'] }}</p>
              <input type="text" class="form-field-input" id="street_line_one"  v-model="brand.address.street_line_one"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.street_line_one') }}</p>
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="street_line_two">
                {{ $t('app.settings.brand_settings.update.fields.street_line_two') }}
              </label>
              <p class="form-field-error" v-if="errors['address.streetLineTwo'] != undefined">{{ errors['address.streetLineTwo'] }}</p>
              <input type="text" class="form-field-input" id="street_line_two"  v-model="brand.address.street_line_two"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.street_line_two') }}</p>
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="city">
                {{ $t('app.settings.brand_settings.update.fields.city') }}
              </label>
              <p class="form-field-error" v-if="errors['address.city'] != undefined">{{ errors['address.city']  }}</p>
              <input type="text" class="form-field-input" id="city"  v-model="brand.address.city"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.city') }}</p>
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="region">
                {{ $t('app.settings.brand_settings.update.fields.region') }}
              </label>
              <p class="form-field-error" v-if="errors['address.region'] != undefined">{{ errors['address.region'] }}</p>
              <input type="text" class="form-field-input" id="region"  v-model="brand.address.region"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.region') }}</p>
            </div>

            <div class="form-field-ctn">
              <label class="form-field-lbl" for="country">
                {{ $t('app.settings.brand_settings.update.fields.country') }}
              </label>
              <p class="form-field-error" v-if="errors['address.country'] != undefined">{{ errors['address.country'] }}</p>
              <input type="text" class="form-field-input" id="country"  v-model="brand.address.country"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.country') }}</p>
            </div>
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="post_code">
                {{ $t('app.settings.brand_settings.update.fields.postcode') }}
              </label>
              <p class="form-field-error" v-if="errors['address.postcode'] != undefined">{{ errors['address.postcode'] }}</p>
              <input type="text" class="form-field-input" id="post_code"  v-model="brand.address.postcode"  />
              <p class="form-field-help">{{ $t('app.settings.brand_settings.update.help_info.postcode') }}</p>
            </div>
          </div>
        </div>
        <div v-if="view === 'notifications'">
          <div class="card-body">
          <div class="grid grid-cols-3">
            <div class="ds">
              <input type="checkbox" id="subscription_creation" v-model="brand.notifications.subscription_creation" />
              <label for="subscription_creation" class="ml-3">{{ $t('app.settings.brand_settings.update.notification.subscription_creation') }}</label>
            </div>
            <div class="ds">
              <input type="checkbox" id="subscription_cancellation" v-model="brand.notifications.subscription_cancellation" />
              <label for="subscription_cancellation" class="ml-3">{{ $t('app.settings.brand_settings.update.notification.subscription_cancellation') }}</label>
            </div>
            <div class="ds">
              <input type="checkbox" id="expiring_card_warning" v-model="brand.notifications.expiring_card_warning" />
              <label for="expiring_card_warning" class="ml-3">{{ $t('app.settings.brand_settings.update.notification.expiring_card_warning') }}</label>
            </div>
            <div class="ds">
              <input type="checkbox" id="expiring_card_warning_day_before" v-model="brand.notifications.expiring_card_warning_day_before" />
              <label for="expiring_card_warning_day_before" class="ml-3">{{ $t('app.settings.brand_settings.update.notification.expiring_card_warning_day_before') }}</label>
            </div>
            <div class="ds">
              <input type="checkbox" id="invoice_created" v-model="brand.notifications.invoice_created" />
              <label for="invoice_created" class="ml-3">{{ $t('app.settings.brand_settings.update.notification.invoice_created') }}</label>
            </div>
            <div class="ds">
              <input type="checkbox" id="invoice_overdue" v-model="brand.notifications.invoice_overdue" />
              <label for="invoice_overdue" class="ml-3">{{ $t('app.settings.brand_settings.update.notification.invoice_overdue') }}</label>
            </div>
            <div class="ds">
              <input type="checkbox" id="quote_created" v-model="brand.notifications.quote_created" />
              <label for="quote_created" class="ml-3">{{ $t('app.settings.brand_settings.update.notification.quote_created') }}</label>
            </div>
          </div></div>
        </div>
        <div class="mt-5 form-field-submit-ctn">
          <SubmitButton :in-progress="sending">{{ $t('app.settings.brand_settings.update.submit_btn') }}</SubmitButton>
        </div>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.brand_settings.update.success_message') }}</p>
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
      ready: false,
      brand: {},
      errors: {},
      sending: false,
      view: "general",
    }
  },
  mounted() {
    var brandId = this.$route.params.id
    axios.get('/app/settings/brand/'+brandId).then(response => {
      this.brand = response.data.brand;
      this.ready = true;
    }).catch(error => {
      if (error.response.status == 404) {
        this.errorMessage = this.$t('app.settings.brand_settings.update.error.not_found')
      } else {
        this.errorMessage = this.$t('app.settings.brand_settings.update.error.unknown')
      }

      this.error = true;
      this.ready = true;
    })
  },
  methods: {
    save: function () {
        var brandId = this.$route.params.id;
        this.sending = true;
        this.errors = {};
        const payload = {
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
          notifications: this.brand.notifications,
          tax_number: this.brand.tax_number,
          tax_rate: this.brand.tax_rate != "" ? this.brand.tax_rate : null,
          digital_services_tax_rate: this.brand.digital_services_tax_rate != "" ? this.brand.digital_services_tax_rate : null,
        };

        axios.post('/app/settings/brand/'+brandId, payload).then(response => {
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
.viewLink {
  @apply font-bold no-underline;
}
.viewLink:hover {
  @apply text-blue-300;
  cursor: pointer;
}
.activeView {
  cursor:  not-allowed;
  color: black !important;
}
</style>