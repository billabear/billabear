<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.country.view.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="text-end m-5">
        <router-link :to="{name: 'app.system.country.edit', params: {id: country.id}}" class="btn--main">{{ $t('app.country.view.edit_button') }}</router-link>
      </div>
      <div>
        <div class="card-body">
          <div class="section-body">
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.country.view.fields.name') }}</dt>
                <dd>{{ country.name }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.country.view.fields.iso_code') }}</dt>
                <dd>{{ country.iso_code }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.country.view.fields.currency') }}</dt>
                <dd>{{ country.currency }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.country.view.fields.threshold') }}</dt>
                <dd>{{ currency(country.threshold) }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.country.view.fields.in_eu') }}</dt>
                <dd>{{ country.in_eu }}</dd>
              </div>
            </dl>
          </div>
        </div>
        <div>

          <h2 class="page-title">{{ $t('app.country.view.tax_rule.title') }}</h2>
          <div class="text-end mr-5">
            <button class="btn--main" @click="showCreate">{{ $t('app.country.view.tax_rule.add') }}</button>
          </div>
          <table class="list-table">
            <thead>
            <tr>
              <th>{{ $t('app.country.view.tax_rule.rate') }}</th>
              <th>{{ $t('app.country.view.tax_rule.type')}}</th>
              <th>{{ $t('app.country.view.tax_rule.start_date') }}</th>
              <th>{{ $t('app.country.view.tax_rule.end_date') }}</th>
              <th>{{ $t('app.country.view.tax_rule.default') }}</th>
              <th></th>
            </tr>
            </thead>
            <tbody v-if="tax_rules.length > 0">
              <tr v-for="rule in tax_rules">
                <td>{{ rule.tax_rate }}</td>
                <td>{{ rule.tax_type.name }}</td>
                <td>{{ rule.valid_from }}</td>
                <td>{{ rule.valid_until }}</td>
                <td>{{ rule.default }}</td>
                <td><button class="btn--secondary" @click="showEdit(rule)">{{ $t('app.country.view.tax_rule.edit') }}</button> </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td colspan="6" class="text-center">{{ $t('app.country.view.tax_rule.no_tax_rules') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </LoadingScreen>

    <VueFinalModal
        v-model="openCountryTaxAdd"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <h3>{{ $t('app.country.view.add_tax_rule.title') }}</h3>
      <label class="form-field-lbl" for="price">
        {{ $t('app.country.view.add_tax_rule.tax_rate') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxRate != undefined">{{ taxRuleErrors.taxRate }}</p>
      <input type="text" class="form-field" v-model="tax_rule.rate" />

      <label class="form-field-lbl" for="price">
        {{ $t('app.country.view.add_tax_rule.tax_type') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxType != undefined">{{ taxRuleErrors.taxType }}</p>
      <select class="form-field" v-model="tax_rule.type">
        <option></option>
        <option v-for="tax_type in tax_types" :value="tax_type">{{ tax_type.name }}</option>
      </select>

      <label class="form-field-lbl" for="valid_from">
        {{ $t('app.country.view.add_tax_rule.valid_from') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validFrom != undefined">{{ taxRuleErrors.validFrom }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_from"  :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="valid_until">
        {{ $t('app.country.view.add_tax_rule.valid_until') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validUntil != undefined">{{ taxRuleErrors.validUntil }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_until"  :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="default">
        {{ $t('app.country.view.add_tax_rule.default') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.default != undefined">{{ taxRuleErrors.default }}</p>
      <input type="checkbox" class="form-field" v-model="tax_rule.default" />
      <SubmitButton :in-progress="creatingTaxRule" @click="createCountryTaxRule" class="btn--main">{{ $t('app.country.view.add_tax_rule.save') }}</SubmitButton>
    </VueFinalModal>


    <VueFinalModal
        v-model="openCountryTaxEdit"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <h3>{{ $t('app.country.view.edit_tax_rule.title') }}</h3>
      <label class="form-field-lbl" for="price">
        {{ $t('app.country.view.edit_tax_rule.tax_rate') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxRate != undefined">{{ taxRuleErrors.taxRate }}</p>
      <input type="text" class="form-field" v-model="tax_rule.tax_rate" />

      <label class="form-field-lbl" for="price">
        {{ $t('app.country.view.edit_tax_rule.tax_type') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxType != undefined">{{ taxRuleErrors.taxType }}</p>
      <select class="form-field" v-model="tax_rule.tax_type">
        <option></option>
        <option v-for="tax_type in tax_types" :value="tax_type">{{ tax_type.name }}</option>
      </select>

      <label class="form-field-lbl" for="valid_from">
        {{ $t('app.country.view.edit_tax_rule.valid_from') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validFrom != undefined">{{ taxRuleErrors.validFrom }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_from"  :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="valid_until">
        {{ $t('app.country.view.edit_tax_rule.valid_until') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validUntil != undefined">{{ taxRuleErrors.validUntil }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_until" :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="default">
        {{ $t('app.country.view.edit_tax_rule.default') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.default != undefined">{{ taxRuleErrors.default }}</p>
      <input type="checkbox" class="form-field" v-model="tax_rule.default" />
      <SubmitButton :in-progress="creatingTaxRule" @click="editCountryTaxRule" class="btn--main">{{ $t('app.country.view.edit_tax_rule.save') }}</SubmitButton>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import {VueFinalModal} from "vue-final-modal";
import {Button, Input, Select} from "flowbite-vue";

export default {
  name: "CountryView",
  components: {Input, Button, VueFinalModal},
  data() {
    return {
      ready: false,
      country: {},
      tax_rules: [],
      tax_types: [],
      original_tax_rule: {},
      openCountryTaxAdd: false,
      openCountryTaxEdit: false,
      tax_rule: {
        rate: 0,
        type: null,
        valid_from: null,
        valid_until: null,
        default: false,
      },
      creatingTaxRule: false,
      taxRuleErrors: {},
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/country/"+id+"/view").then(response => {
      this.country = response.data.country;
      this.tax_rules = response.data.country_tax_rules;
      this.tax_types = response.data.tax_types;
      this.ready = true;
    })
  },
  methods: {
    showEdit: function(tax_rule) {
        this.original_tax_rule = tax_rule;
        this.tax_rule = Object.assign({}, tax_rule);
        this.openCountryTaxEdit = true;
    },
    showCreate: function() {
        this.tax_rule = {
          tax_rate: 0,
          tax_type: null,
          valid_from: null,
          valid_until: null,
          default: false,
        };
        this.openCountryTaxAdd = true;
    },
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
    createCountryTaxRule: function () {
      this.taxRuleErrors = {};
      this.creatingTaxRule = true;

      if (this.tax_rule.type === null) {
        this.taxRuleErrors.taxType = this.$t('app.country.view.add_tax_rule.select_tax_type')
        this.creatingTaxRule = true;
        return;
      }

      const id = this.$route.params.id
      const payload = {
        country: id,
        tax_rate: this.tax_rule.tax_rate,
        tax_type: this.tax_rule.tax_type.id,
        valid_from: this.tax_rule.valid_from,
        valid_until: this.tax_types.valid_until,
        default: this.tax_rule.default,
      }

      axios.post("/app/country/"+id+"/tax-rule", payload).then(response => {
        this.tax_rules.push(response.data);
        this.creatingTaxRule = false;
        this.openCountryTaxAdd = false;
      })
    },

    editCountryTaxRule: function () {
      this.taxRuleErrors = {};
      this.creatingTaxRule = true;

      if (this.tax_rule.type === null) {
        this.taxRuleErrors.taxType = this.$t('app.country.view.add_tax_rule.select_tax_type')
        this.creatingTaxRule = true;
        return;
      }

      const id = this.$route.params.id
      const payload = {
        id: this.tax_rule.id,
        country: id,
        tax_rate: this.tax_rule.tax_rate,
        tax_type: this.tax_rule.tax_type.id,
        valid_from: this.tax_rule.valid_from,
        valid_until: this.tax_rule.valid_until,
        default: this.tax_rule.default,
      }
      for (var key = 0; key < this.tax_rules.length; key++) {
        if (this.tax_rules[key].id === this.tax_rule.id) {
          break;
        }
      }

      axios.post("/app/country/"+id+"/tax-rule/"+this.tax_rule.id+"/edit", payload).then(response => {
        this.tax_rules[key] = response.data;
      })
    },

  }
}
</script>

<style scoped>

</style>