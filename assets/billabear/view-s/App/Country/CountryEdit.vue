<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.country.edit.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="">
        <div class="card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.edit.country.fields.name') }}
            </label>
            <p class="form-field-error" v-if="errors.name != undefined">
              {{ errors.name }}
            </p>
            <input type="text" class="form-field" v-model="country.name" />
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.name') }}
            </p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.edit.country.fields.enabled') }}
            </label>
            <p class="form-field-error" v-if="errors.enabled != undefined">
              {{ errors.enabled }}
            </p>
            <Toggle v-model:value="country.enabled" />
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.enabled') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.edit.country.fields.iso_code') }}
            </label>
            <p class="form-field-error" v-if="errors.isoCode != undefined">
              {{ errors.isoCode }}
            </p>
            {{ country.iso_code }}
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.iso_code') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.edit.country.fields.currency') }}
            </label>
            <p class="form-field-error" v-if="errors.currency != undefined">
              {{ errors.currency }}
            </p>
            <CurrencySelect v-model:value="country.currency" />
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.edit.country.fields.tax_year') }}
            </label>
            <p class="form-field-error" v-if="errors.taxYear != undefined">
              {{ errors.taxYear }}
            </p>
            <VueDatePicker
              class="mt-2"
              v-model:value="country.start_of_tax_year"
              :enable-time-picker="false"
              :format="'dd/MMM'"
              model-type="dd/MMM"
            ></VueDatePicker>
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.tax_year') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.edit.country.fields.threshold') }}
            </label>
            <p class="form-field-error" v-if="errors.threshold != undefined">
              {{ errors.threshold }}
            </p>
            <input
              type="number"
              class="form-field"
              v-model="country.threshold"
            />
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.threshold') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="in_eu">
              {{ $t('app.country.edit.country.fields.in_eu') }}
            </label>
            <p class="form-field-error" v-if="errors.inEu != undefined">
              {{ errors.inEu }}
            </p>
            <input type="checkbox" class="form-field" v-model="country.in_eu" />
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.in_eu') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="collecting">
              {{ $t('app.country.edit.country.fields.collecting') }}
            </label>
            <p class="form-field-error" v-if="errors.collecting != undefined">
              {{ errors.collecting }}
            </p>
            <Toggle v-model:value="country.collecting" />
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.collecting') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.edit.country.fields.tax_number') }}
            </label>
            <p class="form-field-error" v-if="errors.taxNumber != undefined">
              {{ errors.taxNumber }}
            </p>
            <input
              type="text"
              class="form-field"
              v-model="country.tax_number"
            />
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.tax_number') }}
            </p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="threshold">
              {{ $t('app.country.edit.country.fields.transaction_threshold') }}
            </label>
            <p
              class="form-field-error"
              v-if="errors.transactionThreshold != undefined"
            >
              {{ errors.transactionThreshold }}
            </p>
            <input
              type="number"
              class="form-field"
              v-model="country.transaction_threshold"
            />
            <p class="form-field-help">
              {{
                $t('app.country.edit.country.help_info.transaction_threshold')
              }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="threshold_type">
              {{ $t('app.country.edit.country.fields.threshold_type') }}
            </label>
            <p
              class="form-field-error"
              v-if="errors.thresholdType != undefined"
            >
              {{ errors.thresholdType }}
            </p>
            <select v-model="country.threshold_type" class="form-field">
              <option value="rolling">
                {{
                  $t('app.country.edit.country.fields.threshold_types.rolling')
                }}
              </option>
              <option value="calendar">
                {{
                  $t('app.country.edit.country.fields.threshold_types.calendar')
                }}
              </option>
              <option value="rolling_quarterly">
                {{
                  $t(
                    'app.country.edit.country.fields.threshold_types.rolling_quarterly'
                  )
                }}
              </option>
              <option value="rolling_accounting">
                {{
                  $t(
                    'app.country.edit.country.fields.threshold_types.rolling_accounting'
                  )
                }}
              </option>
            </select>
            <p class="form-field-help">
              {{ $t('app.country.edit.country.help_info.threshold_type') }}
            </p>
          </div>
        </div>
      </div>
      <div class="mt-3">
        <SubmitButton :in-progress="sending" @click="send">{{
          $t('app.country.edit.update_button')
        }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from 'axios'
import CurrencySelect from '../../../components/app/Forms/CurrencySelect.vue'
import { Toggle } from 'flowbite-vue'

export default {
  name: 'CountryEdit',
  components: { Toggle, CurrencySelect },
  data() {
    return {
      ready: false,
      country: {},
      errors: {},
      sending: false,
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get('/app/country/' + id + '/view').then((response) => {
      this.country = response.data.country
      this.ready = true
    })
  },
  methods: {
    send: function () {
      this.errors = {}
      this.sending = true
      axios
        .post('/app/country/' + this.country.id + '/edit', this.country)
        .then((response) => {
          this.$router.push({
            name: 'app.finance.country.view',
            params: { id: this.country.id },
          })
          this.sending = false
        })
        .catch((error) => {
          if (error.response) {
            this.errors = error.response.data.errors
          }
          this.sending = false
        })
    },
  },
}
</script>

<style scoped></style>
