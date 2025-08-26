<template>
  <div>
    <h1 class="page-title">{{ $t('app.price.create.title') }}</h1>

    <form @submit.prevent="send">
      <div class="card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="amount">
            {{ $t('app.price.create.type') }}
          </label>

          <p class="form-field-error" v-if="errors.type != undefined">
            {{ errors.type }}
          </p>
          <select class="form-field" v-model="price.type">
            <option value="fixed_price">
              {{ $t('app.price.create.types.fixed_price') }}
            </option>
            <option value="package">
              {{ $t('app.price.create.types.package') }}
            </option>
            <option value="per_unit">
              {{ $t('app.price.create.types.per_unit') }}
            </option>
            <option value="tiered_volume">
              {{ $t('app.price.create.types.tiered_volume') }}
            </option>
            <option value="tiered_graduated">
              {{ $t('app.price.create.types.tiered_graduated') }}
            </option>
          </select>
        </div>

        <div class="form-field-ctn" v-if="showAmount">
          <label class="form-field-lbl" for="amount">
            {{ $t('app.price.create.amount') }}
          </label>
          <p class="form-field-error" v-if="errors.amount != undefined">
            {{ errors.amount }}
          </p>
          <CurrencyInput v-model:value="price.amount" />
        </div>

        <div class="form-field-ctn" v-if="showUnits">
          <label class="form-field-lbl" for="units">
            {{ $t('app.price.create.units') }}
          </label>
          <p class="form-field-error" v-if="errors.units != undefined">
            {{ errors.units }}
          </p>
          <input type="number" v-model="price.units" class="form-field-input" />
        </div>

        <div class="my-3" v-if="showTiers">
          <h3 class="text-xl">
            {{ $t('app.price.create.tiers') }}
          </h3>

          <table>
            <thead>
              <tr>
                <th>{{ $t('app.price.create.tiers_fields.first_unit') }}</th>
                <th>{{ $t('app.price.create.tiers_fields.last_unit') }}</th>
                <th>{{ $t('app.price.create.tiers_fields.unit_price') }}</th>
                <th>{{ $t('app.price.create.tiers_fields.flat_fee') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(tier, key) in tiers">
                <td>
                  <input
                    disabled
                    v-model="tier.first_unit"
                    class="form-field-input"
                  />
                </td>
                <td>
                  <p
                    class="form-field-error"
                    v-if="
                      errors.tiers != undefined &&
                      errors.tiers[key].last_unit != undefined
                    "
                  >
                    {{ errors.tiers[key].last_unit }}
                  </p>

                  <input
                    v-model="tier.last_unit"
                    class="form-field-input"
                    :class="{ 'error-field': validTiers === false }"
                    type="number"
                    v-if="key !== tiers.length - 1"
                  />
                  <input disabled class="form-field-input" value="∞" v-else />
                </td>
                <td>
                  <p
                    class="form-field-error"
                    v-if="
                      errors.tiers != undefined &&
                      errors.tiers[key].unit_price != undefined
                    "
                  >
                    {{ errors.tiers[key].unit_price }}
                  </p>

                  <CurrencyInput v-model:value="tier.unit_price" />
                </td>
                <td>
                  <p
                    class="form-field-error"
                    v-if="
                      errors.tiers != undefined &&
                      errors.tiers[key].flat_fee != undefined
                    "
                  >
                    {{ errors.tiers[key].flat_fee }}
                  </p>

                  <CurrencyInput v-model:value="tier.flat_fee" />
                </td>
              </tr>
            </tbody>
          </table>

          <button type="button" class="btn--main" @click="addTier">Add</button>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="currency">
            {{ $t('app.price.create.currency') }}
          </label>
          <p class="form-field-error" v-if="errors.currency != undefined">
            {{ errors.currency }}
          </p>
          <CurrencySelect v-model:value="price.currency" />
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.currency') }}
          </p>
        </div>
        <div class="form-field-ctn" v-if="showUsage">
          <label class="form-field-lbl" for="usage">
            {{ $t('app.price.create.usage') }}
          </label>
          <p class="form-field-error" v-if="errors.usage != undefined">
            {{ errors.usage }}
          </p>
          <input
            type="checkbox"
            class="fodsrm-field-input"
            id="usage"
            v-model="price.usage"
          />
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.usage') }}
          </p>
        </div>

        <div class="form-field-ctn" v-if="price.usage">
          <label class="form-field-lbl">{{
            $t('app.price.create.metric')
          }}</label>
          <p class="form-field-error" v-if="errors.metric != undefined">
            {{ errors.metric }}
          </p>
          <select
            class="form-field"
            v-model="price.metric"
            v-if="metrics.length > 0"
          >
            <option v-for="metric in metrics" :value="metric.id">
              {{ metric.name }}
            </option>
          </select>
          <router-link :to="{ name: 'app.metric.create' }" v-else>{{
            $t('app.price.create.create_metric')
          }}</router-link>
        </div>

        <div class="form-field-ctn" v-if="price.usage">
          <label class="form-field-lbl" for="name">
            {{ $t('app.price.create.metric_type') }}
          </label>
          <p class="form-field-error" v-if="errors.metric_type != undefined">
            {{ errors.metric_type }}
          </p>
          <select class="form-field" v-model="price.metric_type">
            <option value="resettable">
              {{ $t('app.price.create.metric_types.resettable') }}
            </option>
            <option value="continuous">
              {{ $t('app.price.create.metric_types.continuous') }}
            </option>
          </select>
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.metric_type') }}
          </p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="recurring">
            {{ $t('app.price.create.recurring') }}
          </label>
          <p class="form-field-error" v-if="errors.recurring != undefined">
            {{ errors.recurring }}
          </p>
          <input
            type="checkbox"
            class="form-field-input"
            id="recurring"
            v-model="price.recurring"
          />
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.recurring') }}
          </p>
        </div>
        <div class="form-field-ctn" v-if="price.recurring">
          <label class="form-field-lbl" for="schedule">
            {{ $t('app.price.create.schedule_label') }}
          </label>
          <p class="form-field-error" v-if="errors.schedule != undefined">
            {{ errors.schedule }}
          </p>
          <select class="form-field-input" id="name" v-model="price.schedule">
            <option :value="null"></option>
            <option value="week">
              {{ $t('app.price.create.schedule.week') }}
            </option>
            <option value="month">
              {{ $t('app.price.create.schedule.month') }}
            </option>
            <option value="year">
              {{ $t('app.price.create.schedule.year') }}
            </option>
          </select>
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.schedule') }}
          </p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="including_tax">
            {{ $t('app.price.create.including_tax') }}
          </label>
          <p class="form-field-error" v-if="errors.including_tax != undefined">
            {{ errors.including_tax }}
          </p>
          <input
            type="checkbox"
            class="fodsrm-field-input"
            id="including_tax"
            v-model="price.including_tax"
          />
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.including_tax') }}
          </p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="public">
            {{ $t('app.price.create.public') }}
          </label>
          <p class="form-field-error" v-if="errors.public != undefined">
            {{ errors.public }}
          </p>
          <input
            type="checkbox"
            class="fodsrm-field-input"
            id="public"
            v-model="price.public"
          />
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.public') }}
          </p>
        </div>
      </div>

      <div class="my-3 form-field-ctn">
        <p @click="showAdvance = !showAdvance" class="cursor-pointer">
          <i class="fa-solid fa-caret-up" v-if="showAdvance"></i>
          <i class="fa-solid fa-caret-down" v-else></i>
          <span class="ml-2">{{ $t('app.price.create.show_advanced') }}</span>
        </p>
      </div>
      <div class="card-body mb-3" v-if="showAdvance">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.price.create.external_reference') }}
          </label>
          <p
            class="form-field-error"
            v-if="errors.external_reference != undefined"
          >
            {{ errors.external_reference }}
          </p>
          <input
            type="text"
            class="form-field-input"
            id="external_reference"
            v-model="price.external_reference"
          />
          <p class="form-field-help">
            {{ $t('app.price.create.help_info.external_reference') }}
          </p>
        </div>
      </div>

      <p class="text-green-500 font-weight-bold" v-if="success">
        {{ $t('app.product.create.success_message') }}
      </p>
      <div class="form-field-submit-ctn">
        <SubmitButton :in-progress="sendingInProgress">{{
          $t('app.product.create.submit_btn')
        }}</SubmitButton>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import currency from 'currency.js'
import CurrencySelect from '../../../components/app/Forms/CurrencySelect.vue'
import CurrencyInput from '../../../components/app/Forms/CurrencyInput.vue'
import SectionFeatures from '../SubscriptionPlan/Parts/SectionFeatures.vue'
import { Button, Select } from 'flowbite-vue'
import { timer } from 'd3'

export default {
  name: 'PriceCreate',
  components: {
    Button,
    Select,
    SectionFeatures,
    CurrencyInput,
    CurrencySelect,
  },
  data() {
    return {
      price: {
        amount: 0,
        currency: 'EUR',
        recurring: true,
        schedule: null,
        external_reference: null,
        including_tax: false,
        public: true,
        type: 'fixed_price',
        usage: false,
        units: null,
      },
      rawTiers: [
        { first_unit: 1, last_unit: 1, unit_price: 0, flat_fee: 0 },
        { first_unit: 1, last_unit: null, unit_price: 0, flat_fee: 0 },
      ],
      errors: {},
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      metrics: [],
    }
  },
  computed: {
    showAmount: function () {
      return !(
        this.price.type === 'tiered_volume' ||
        this.price.type === 'tiered_graduated'
      )
    },
    showTiers: function () {
      return (
        this.price.type === 'tiered_volume' ||
        this.price.type === 'tiered_graduated'
      )
    },
    showUnits: function () {
      return this.price.type === 'package'
    },
    showUsage: function () {
      return this.price.type !== 'fixed_price'
    },
    validTiers: function () {
      let valid = true
      this.rawTiers.forEach((tier) => {
        if (!valid) {
          return
        }
        if (tier.first_unit > tier.last_unit && tier.last_unit !== null) {
          valid = false
          return
        }
      })

      return valid
    },
    tiers: function () {
      const output = []
      let lastUnit = 0

      this.rawTiers.forEach((tier) => {
        tier.first_unit = lastUnit + 1
        output.push(tier)
        lastUnit = tier.last_unit
      })

      const len = this.rawTiers.length - 1
      this.rawTiers[len].last_unit = null
      return output
    },
  },
  methods: {
    addTier: function () {
      let firstUnit = 1
      let lastUnit = 2
      let secondLast = null
      this.rawTiers.forEach((tier) => {
        secondLast = lastUnit
        firstUnit = tier.first_unit
        if (tier.last_unit === null) {
          tier.last_unit = tier.first_unit + 1
        }
        lastUnit = tier.last_unit
      })
      this.rawTiers.push({
        first_unit: lastUnit + 1,
        last_unit: lastUnit + 2,
        unit_price: 0,
        flat_fee: 0,
      })
    },
    currency: function (value) {
      return currency(value, { fromCents: true })
    },
    send: function () {
      const productId = this.$route.params.productId
      this.sendingInProgress = true
      this.success = false
      this.errors = {}

      if (this.showTiers) {
        this.price.tiers = this.tiers
        this.price.amount = null
      }

      axios
        .post('/app/product/' + productId + '/price', this.price)
        .then((response) => {
          this.sendingInProgress = false
          this.success = true
        })
        .catch((error) => {
          this.sendingInProgress = false
          this.errors = error.response.data.errors
          this.success = false
        })
    },
  },
  mounted() {
    const productId = this.$route.params.productId
    axios.get('/app/product/' + productId + '/price').then((response) => {
      this.metrics = response.data.metrics
    })
  },
}
</script>

<style scoped></style>
