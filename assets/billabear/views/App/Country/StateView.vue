<template>
  <div>
    <div class="grid grid-cols-2">
      <h1 class="ml-5 mt-5 page-title">{{ $t('app.state.view.title') }}</h1>

      <div class="text-end mt-5" v-if="ready">
        <router-link :to="{'name': 'app.finance.state.edit', params: {countryId: countryId,stateId: state.id}}" class="btn--main">{{ $t('app.state.view.edit') }}</router-link>
      </div>
    </div>

    <LoadingScreen :ready="ready">
      <div class="">
        <div class="card-body">
          <div class="section-body">
            <dl class="detail-list">
              <div>
                <dt>{{ $t('app.state.view.fields.name') }}</dt>
                <dd>{{ state.name }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.state.view.fields.code') }}</dt>
                <dd>{{ state.code }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.state.view.fields.collecting') }}</dt>
                <dd>{{ state.collecting }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.state.view.fields.threshold') }}</dt>
                <dd><Currency :amount="state.threshold" :currency="state.country.currency" /></dd>
              </div>
              <div>
                <dt>{{ $t('app.state.view.fields.transaction_threshold') }}</dt>
                <dd>{{ state.transaction_threshold }}</dd>
              </div>
              <div>
                <dt>{{ $t('app.state.view.fields.threshold_type') }}</dt>
                <dd>{{ state.threshold_type }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2">
        <h2 class="page-title">{{ $t('app.state.view.tax_rule.title') }}</h2>
        <div class="text-end mt-5">
          <button class="btn--main" @click="showCreate">{{ $t('app.state.view.tax_rule.add') }}</button>
        </div>
      </div>
      <div class="rounded-lg bg-white shadow p-3">
        <table class="w-full">
          <thead>
          <tr class="border-b border-black">
            <th class="text-left pb-2">{{ $t('app.state.view.tax_rule.rate') }}</th>
          <th class="text-left pb-2">{{ $t('app.state.view.tax_rule.type')}}</th>
          <th class="text-left pb-2">{{ $t('app.state.view.tax_rule.start_date') }}</th>
          <th class="text-left pb-2">{{ $t('app.state.view.tax_rule.end_date') }}</th>
          <th class="text-left pb-2">{{ $t('app.state.view.tax_rule.default') }}</th>
          <th></th>
        </tr>
        </thead>
        <tbody v-if="tax_rules.length > 0">
        <tr v-for="rule in tax_rules">
          <td class="py-3">{{ rule.tax_rate }}</td>
          <td class="py-3">{{ rule.tax_type.name }}</td>
          <td class="py-3">{{ rule.valid_from }}</td>
          <td class="py-3">{{ rule.valid_until }}</td>
          <td class="py-3">{{ rule.default }}</td>
          <td class="py-3"><button class="btn--secondary" @click="showEdit(rule)">{{ $t('app.country.view.tax_rule.edit') }}</button> </td>
        </tr>
        </tbody>
        <tbody v-else>
        <tr>
          <td colspan="6" class="py-3 text-center">{{ $t('app.country.view.tax_rule.no_tax_rules') }}</td>
        </tr>
        </tbody>
      </table>
      </div>


    </LoadingScreen>
    <VueFinalModal
        v-model="openCountryTaxAdd"
        class="flex justify-center items-center"
        content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
    >
      <h3>{{ $t('app.state.view.add_tax_rule.title') }}</h3>
      <label class="form-field-lbl" for="price">
        {{ $t('app.state.view.add_tax_rule.tax_rate') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxRate != undefined">{{ taxRuleErrors.taxRate }}</p>
      <input type="text" class="form-field" v-model="tax_rule.tax_rate" />

      <label class="form-field-lbl" for="price">
        {{ $t('app.country.view.add_tax_rule.tax_type') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxType != undefined">{{ taxRuleErrors.taxType }}</p>
      <select class="form-field" v-model="tax_rule.tax_type">
        <option></option>
        <option v-for="tax_type in tax_types" :value="tax_type">{{ tax_type.name }}</option>
      </select>

      <label class="form-field-lbl" for="valid_from">
        {{ $t('app.state.view.add_tax_rule.valid_from') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validFrom != undefined">{{ taxRuleErrors.validFrom }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_from"  :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="valid_until">
        {{ $t('app.state.view.add_tax_rule.valid_until') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validUntil != undefined">{{ taxRuleErrors.validUntil }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_until"  :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="default">
        {{ $t('app.state.view.add_tax_rule.default') }}
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
      <h3>{{ $t('app.state.view.edit_tax_rule.title') }}</h3>
      <label class="form-field-lbl" for="price">
        {{ $t('app.state.view.edit_tax_rule.tax_rate') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxRate != undefined">{{ taxRuleErrors.taxRate }}</p>
      <input type="text" class="form-field" v-model="tax_rule.tax_rate" />

      <label class="form-field-lbl" for="price">
        {{ $t('app.state.view.edit_tax_rule.tax_type') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.taxType != undefined">{{ taxRuleErrors.taxType }}</p>
      <select class="form-field" v-model="tax_rule.tax_type">
        <option></option>
        <option v-for="tax_type in tax_types" :value="tax_type">{{ tax_type.name }}</option>
      </select>

      <label class="form-field-lbl" for="valid_from">
        {{ $t('app.state.view.edit_tax_rule.valid_from') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validFrom != undefined">{{ taxRuleErrors.validFrom }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_from"  :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="valid_until">
        {{ $t('app.state.view.edit_tax_rule.valid_until') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.validUntil != undefined">{{ taxRuleErrors.validUntil }}</p>
      <VueDatePicker  class="mt-2" v-model="tax_rule.valid_until" :enable-time-picker="false"></VueDatePicker>

      <label class="form-field-lbl" for="default">
        {{ $t('app.state.view.edit_tax_rule.default') }}
      </label>
      <p class="form-field-error" v-if="taxRuleErrors.default != undefined">{{ taxRuleErrors.isDefault }}</p>
      <input type="checkbox" class="form-field" v-model="tax_rule.default" />
      <SubmitButton :in-progress="creatingTaxRule" @click="editCountryTaxRule" class="btn--main">{{ $t('app.state.view.edit_tax_rule.save') }}</SubmitButton>
    </VueFinalModal>
  </div>
</template>

<script>
import axios from "axios";
import Currency from "../../../components/app/Currency.vue";
import {VueFinalModal} from "vue-final-modal";

export default {
  name: "StateView",
  components: {VueFinalModal, Currency},
  data() {
    return {
      ready: false,
      state: {},
      tax_rules: [],
      tax_types: [],
      openCountryTaxAdd: false,
      openCountryTaxEdit: false,
      creatingTaxRule: false,
      tax_rule: {
        rate: 0,
        type: null,
        valid_from: null,
        valid_until: null,
        default: false,
      },
      taxRuleErrors: {},
      original_tax_rule: {},
      countryId: null,
    }
  },
  mounted() {

    const id = this.$route.params.countryId
    this.countryId = id;
    const stateId = this.$route.params.stateId
    axios.get("/app/country/"+id+"/state/"+stateId+"/view").then(response => {
      this.state = response.data.state;
      this.tax_rules = response.data.tax_rules;
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
        tax_type: {id: null},
        valid_from: null,
        valid_until: null,
        default: false,
      };
      this.openCountryTaxAdd = true;
    },
    createCountryTaxRule: function () {
      this.taxRuleErrors = {};
      this.creatingTaxRule = true;

      if (this.tax_rule.type === null) {
        this.taxRuleErrors.taxType = this.$t('app.country.view.add_tax_rule.select_tax_type')
        this.creatingTaxRule = true;
        return;
      }

      const countryId = this.$route.params.countryId
      const stateId = this.$route.params.stateId
      const payload = {
        country: countryId,
        state: stateId,
        tax_rate: Number(this.tax_rule.tax_rate),
        tax_type: this.tax_rule.tax_type.id,
        valid_from: this.tax_rule.valid_from,
        valid_until: this.tax_types.valid_until,
        default: this.tax_rule.default,
      }


      axios.post("/app/country/"+countryId+"/state/"+stateId+"/tax-rule", payload).then(response => {
        this.tax_rules.push(response.data);
        this.creatingTaxRule = false;
        this.openCountryTaxAdd = false;
      }).catch(error => {
        this.taxRuleErrors = error.response.data.errors;
        this.creatingTaxRule = false;
        this.success = false;
      })
    },

    editCountryTaxRule: function () {
      this.taxRuleErrors = {};
      this.creatingTaxRule = true;

      if (this.tax_rule.type === null) {
        this.taxRuleErrors.taxType = this.$t('app.state.view.add_tax_rule.select_tax_type')
        this.creatingTaxRule = true;
        return;
      }

      const id = this.$route.params.countryId
      const stateId = this.$route.params.stateId
      const payload = {
        id: this.tax_rule.id,
        country: id,
        state: stateId,
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

      axios.post("/app/country/"+id+"/state/"+stateId+"/tax-rule/"+this.tax_rule.id+"/edit", payload).then(response => {
        this.tax_rules[key] = response.data;
        this.creatingTaxRule = false;
        this.openCountryTaxEdit = false;
      }).catch(error => {
        this.taxRuleErrors = error.response.data.errors;
        this.creatingTaxRule = false;
        this.success = false;
      })
    },
  }
}
</script>

<style scoped>

</style>
