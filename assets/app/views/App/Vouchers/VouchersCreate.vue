<template xmlns="http://www.w3.org/1999/html">
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.vouchers.create.title') }}</h1>
  </div>
  <div v-if="!error">
    <LoadingScreen :ready="ready">
      <form @submit.prevent="send">
      <div class="p-5">
      <div class="card-body">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.vouchers.create.fields.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field-input" id="name" v-model="voucher.name" />
          <p class="form-field-help">{{ $t('app.vouchers.create.help_info.name') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl">
            {{ $t('app.vouchers.create.fields.type')}}
          </label>
          <p class="form-field-error" v-if="errors.type != undefined">{{ errors.type }}</p>
          <select v-model="voucher.type" class="form-field">
            <option value="percentage">{{ $t('app.vouchers.create.fields.type_percentage') }}</option>
            <option value="fixed_credit">{{ $t('app.vouchers.create.fields.type_fixed_credit') }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.vouchers.create.help_info.type') }}</p>
        </div>

        <div v-if="voucher.type === 'percentage'">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="percentage">
              {{ $t('app.vouchers.create.fields.percentage') }}
            </label>
            <p class="form-field-error" v-if="errors.percentage != undefined">{{ errors.percentage }}</p>
            <input type="text" class="form-field-input" id="percentage" v-model="voucher.percentage" />
            <p class="form-field-help">{{ $t('app.vouchers.create.help_info.percentage') }}</p>
          </div>
        </div>
        <div v-if="voucher.type === 'fixed_credit'">
          <div class="form-field-ctn" v-for="(currency, key) in currencies">
            <label class="form-field-lbl" for="percentage">
              {{ $t('app.vouchers.create.fields.amount', {currency: currency}) }}
            </label>
            <p class="form-field-error" v-if="errors['amounts['+key+'].amount'] != undefined">{{ errors['amounts['+key+'].amount'] }}</p>
            <input type="text" class="form-field-input"  v-model="voucher.amounts[key].amount" />
            <p class="form-field-help">{{ $t('app.vouchers.create.help_info.amount', {currency: currency}) }}</p>
          </div>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl">
            {{ $t('app.vouchers.create.fields.entry_type')}}
          </label>
          <p class="form-field-error" v-if="errors.entryType != undefined">{{ errors.entryType }}</p>
          <select v-model="voucher.entry_type" class="form-field">
            <option value="manual">{{ $t('app.vouchers.create.fields.entry_type_manual') }}</option>
            <option value="automatic">{{ $t('app.vouchers.create.fields.entry_type_automatic') }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.vouchers.create.help_info.entry_type') }}</p>
        </div>


        <div class="form-field-ctn" v-if="voucher.entry_type === 'manual'">
          <label class="form-field-lbl" for="code">
            {{ $t('app.vouchers.create.fields.code') }}
          </label>
          <p class="form-field-error" v-if="errors.code != undefined">{{ errors.code }}</p>
          <input type="text" class="form-field-input" id="code" v-model="voucher.code" />
          <p class="form-field-help">{{ $t('app.vouchers.create.help_info.code') }}</p>
        </div>

        <div class="form-field-ctn" v-if="voucher.entry_type === 'automatic'">
          <label class="form-field-lbl" for="entry_event">
            {{ $t('app.vouchers.create.fields.entry_event') }}
          </label>
          <p class="form-field-error" v-if="errors.entryEvent != undefined">{{ errors.entryEvent }}</p>
          <select class="form-field-input" id="entry_event" v-model="voucher.entry_event">
            <option value="expired_card_added">{{ $t('app.vouchers.create.fields.event_expired_card_added') }}</option>
          </select>
          <p class="form-field-help">{{ $t('app.vouchers.create.help_info.entry_event') }}</p>
        </div>

      </div>
        <div class="mt-5">
          <SubmitButton :in-progress="inProgress">{{ $t('app.vouchers.create.submit') }}</SubmitButton>
          <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.vouchers.create.success_message') }}</p>
        </div>
  </div>
      </form>
    </LoadingScreen>
  </div>
  <div v-else>
    {{ $t('app.vouchers.create.error_message') }}
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "VouchersCreate",
  data() {
    return {
      ready: false,
      voucher: {
        amounts: []
      },
      currencies: [],
      error: false,
      errors: {},
      inProgress: false,
      success: false
    }
  },
  mounted() {
    axios.get('/app/voucher/create').then(response => {
      this.currencies = response.data.currencies;

      for (var currency of this.currencies) {
        this.voucher.amounts.push({
          currency: currency,
          amount: 0
        })
      }

      this.ready = true;
    }).catch(error => {
      this.error = true;
    })
  },
  methods: {
    send: function () {
      this.inProgress = true;
      if (this.voucher.type === 'percentage') {
        this.voucher.amounts = [];
      }
      this.errors={};
      axios.post("/app/voucher", this.voucher).then(response => {
        this.inProgress = false;
        this.success = true;
      }).catch(error => {
        this.inProgress = false;
        this.errors = error.response.data.errors;
      })
    }
  }
}
</script>

<style scoped>

</style>