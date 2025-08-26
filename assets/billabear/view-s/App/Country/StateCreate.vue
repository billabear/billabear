<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.state.create.title') }}</h1>

    <div class="card-body">
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.state.create.state.fields.name') }}
        </label>
        <p class="form-field-error" v-if="errors.name != undefined">
          {{ errors.name }}
        </p>
        <input type="text" class="form-field" v-model="state.name" />
        <p class="form-field-help">
          {{ $t('app.state.create.state.help_info.name') }}
        </p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="code">
          {{ $t('app.state.create.state.fields.code') }}
        </label>
        <p class="form-field-error" v-if="errors.code != undefined">
          {{ errors.code }}
        </p>
        <input type="text" class="form-field" v-model="state.code" />
        <p class="form-field-help">
          {{ $t('app.state.create.state.help_info.code') }}
        </p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="collecting">
          {{ $t('app.state.create.state.fields.collecting') }}
        </label>
        <p class="form-field-error" v-if="errors.collecting != undefined">
          {{ errors.collecting }}
        </p>
        <Toggle v-model:value="state.collecting" />
        <p class="form-field-help">
          {{ $t('app.state.create.state.help_info.collecting') }}
        </p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="threshold">
          {{ $t('app.state.create.state.fields.threshold') }}
        </label>
        <p class="form-field-error" v-if="errors.code != undefined">
          {{ errors.threshold }}
        </p>
        <CurrencyInput v-model:value="state.threshold" />
        <p class="form-field-help">
          {{ $t('app.state.create.state.help_info.threshold') }}
        </p>
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="threshold">
          {{ $t('app.state.edit.state.fields.transaction_threshold') }}
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
          v-model="state.transaction_threshold"
        />
        <p class="form-field-help">
          {{ $t('app.state.edit.state.help_info.transaction_threshold') }}
        </p>
      </div>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="threshold_type">
          {{ $t('app.state.edit.state.fields.threshold_type') }}
        </label>
        <p class="form-field-error" v-if="errors.thresholdType != undefined">
          {{ errors.thresholdType }}
        </p>
        <select v-model="state.threshold_type" class="form-field">
          <option value="rolling">
            {{ $t('app.state.edit.state.fields.threshold_types.rolling') }}
          </option>
          <option value="calendar">
            {{ $t('app.state.edit.state.fields.threshold_types.calendar') }}
          </option>
          <option value="rolling_quarterly">
            {{
              $t(
                'app.state.edit.state.fields.threshold_types.rolling_quarterly'
              )
            }}
          </option>
          <option value="rolling_accounting">
            {{
              $t(
                'app.state.edit.state.fields.threshold_types.rolling_accounting'
              )
            }}
          </option>
        </select>
        <p class="form-field-help">
          {{ $t('app.state.edit.state.help_info.threshold_type') }}
        </p>
      </div>
    </div>
    <SubmitButton class="mt-3" :in-progress="sending" @click="sendCreate">{{
      $t('app.state.create.create_button')
    }}</SubmitButton>
  </div>
</template>

<script>
import { Toggle } from 'flowbite-vue'
import Currency from '../../../components/app/Currency.vue'
import CurrencyInput from '../../../components/app/Forms/CurrencyInput.vue'
import axios from 'axios'

export default {
  name: 'StateCreate',
  components: { CurrencyInput, Currency, Toggle },
  data() {
    return {
      state: {
        name: '',
        code: '',
        collecting: false,
        threshold: 0,
        country: '',
      },
      errors: {},
      sending: false,
    }
  },
  mounted() {
    this.state.country = this.$route.params.id
  },
  methods: {
    sendCreate: function () {
      const id = this.$route.params.id
      this.sending = true
      this.errors = {}
      axios
        .post('/app/country/' + id + '/state', this.state)
        .then((response) => {
          this.$router.push({
            name: 'app.finance.state.view',
            params: { countryId: id, stateId: response.data.id },
          })
        })
        .catch((error) => {
          if (error.response != undefined) {
            this.errors = error.response.data.errors
            this.sending = false
          }
        })
    },
  },
}
</script>

<style scoped></style>
