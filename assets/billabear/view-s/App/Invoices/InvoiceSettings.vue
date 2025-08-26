<template>
  <div>
    <h1 class="page-title">{{ $t('app.invoices.settings.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="invoice_number_generation">
            {{
              $t(
                'app.settings.system_settings.update.fields.invoice_number_generation'
              )
            }}
          </label>
          <p
            class="form-field-error"
            v-if="errors.invoiceNumberGeneration != undefined"
          >
            {{ errors.invoiceNumberGeneration }}
          </p>
          <select
            class="form-field"
            id="timezone"
            v-model="settings.invoice_number_generation"
          >
            <option value="random">
              {{
                $t(
                  'app.settings.system_settings.update.invoice_number_generation.random'
                )
              }}
            </option>
            <option value="subsequential">
              {{
                $t(
                  'app.settings.system_settings.update.invoice_number_generation.subsequential'
                )
              }}
            </option>
            <option value="format">
              {{
                $t(
                  'app.settings.system_settings.update.invoice_number_generation.format'
                )
              }}
            </option>
          </select>
          <p class="form-field-help">
            {{
              $t(
                'app.settings.system_settings.update.help_info.invoice_number_generation'
              )
            }}
          </p>
        </div>

        <div
          class="form-field-ctn"
          v-if="settings.invoice_number_generation !== 'random'"
        >
          <label class="form-field-lbl" for="subsequential_number">
            {{
              $t(
                'app.settings.system_settings.update.fields.subsequential_number'
              )
            }}
          </label>
          <input
            type="number"
            class="form-field"
            v-model="settings.subsequential_number"
          />
          <p class="form-field-help">
            {{
              $t(
                'app.settings.system_settings.update.help_info.subsequential_number'
              )
            }}
          </p>
        </div>

        <div
          class="form-field-ctn"
          v-if="settings.invoice_number_generation === 'format'"
        >
          <label class="form-field-lbl" for="format">
            {{ $t('app.settings.system_settings.update.fields.format') }}
          </label>
          <input type="text" class="form-field" v-model="settings.format" />
          <p class="form-field-help">
            {{ $t('app.settings.system_settings.update.help_info.format') }}
          </p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="default_invoice_due_time">
            {{
              $t(
                'app.settings.system_settings.update.fields.default_invoice_due_time'
              )
            }}
          </label>
          <select
            class="form-field"
            id="default_invoice_due_time"
            v-model="settings.default_invoice_due_time"
          >
            <option value="30 days">
              {{
                $t(
                  'app.settings.system_settings.update.default_invoice_due_time.30_days'
                )
              }}
            </option>
            <option value="60 days">
              {{
                $t(
                  'app.settings.system_settings.update.default_invoice_due_time.60_days'
                )
              }}
            </option>
            <option value="90 days">
              {{
                $t(
                  'app.settings.system_settings.update.default_invoice_due_time.90_days'
                )
              }}
            </option>
            <option value="120 days">
              {{
                $t(
                  'app.settings.system_settings.update.default_invoice_due_time.120_days'
                )
              }}
            </option>
          </select>
          <p class="form-field-help">
            {{
              $t(
                'app.settings.system_settings.update.help_info.default_invoice_due_time'
              )
            }}
          </p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="default_invoice_due_time">
            {{
              $t(
                'app.settings.system_settings.update.fields.invoice_generation'
              )
            }}
          </label>
          <select
            class="form-field"
            id="default_invoice_due_time"
            v-model="settings.invoice_generation"
          >
            <option value="periodically">
              {{
                $t(
                  'app.settings.system_settings.update.invoice_generation_types.periodically'
                )
              }}
            </option>
            <option value="end_of_month">
              {{
                $t(
                  'app.settings.system_settings.update.invoice_generation_types.end_of_month'
                )
              }}
            </option>
          </select>
          <p class="form-field-help">
            {{
              $t(
                'app.settings.system_settings.update.help_info.invoice_generation'
              )
            }}
          </p>
        </div>
      </div>
      <div class="mt-5">
        <p class="text-green-500 font-weight-bold mb-2" v-if="success">
          {{ $t('app.settings.system_settings.update.success_message') }}
        </p>
        <SubmitButton :in-progress="sending" @click="save">{{
          $t('app.invoices.settings.update')
        }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'InvoiceSettings',
  data() {
    return {
      sending: false,
      ready: false,
      success: false,
      settings: {},
      errors: {},
    }
  },
  mounted() {
    axios.get('/app/invoice/settings').then((response) => {
      this.settings = response.data.invoice_settings
      this.ready = true
    })
  },
  methods: {
    save: function () {
      this.sending = true
      this.errors = {}
      axios
        .post('/app/invoice/settings', this.settings)
        .then((response) => {
          this.sending = false
          this.success = true
        })
        .catch((error) => {
          this.errors = error.response.data.errors
          this.sending = false
          this.success = false
        })
    },
  },
}
</script>

<style scoped></style>
