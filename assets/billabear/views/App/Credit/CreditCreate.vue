<template>
  <div>
    <h1 class="page-title">{{ $t('app.credit.create.title') }}</h1>

    <form @submit.prevent="send">

        <div class="mt-3 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="amount">
              {{ $t('app.credit.create.amount') }}
            </label>
            <p class="form-field-error" v-if="errors.amount != undefined">{{ errors.amount }}</p>
            <input type="number" class="form-field-input" id="amount" v-model="creditNote.amount" />
            <p class="form-field-help">{{ $t('app.credit.create.help_info.amount') }}</p>
            <p class="form-field-help">{{ $t('app.credit.create.help_info.display_amount', {amount: currency(creditNote.amount)}) }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="type">
              {{ $t('app.credit.create.type') }}
            </label>
            <p class="form-field-error" v-if="errors.type != undefined">{{ errors.type }}</p>
            <select class="form-field" id="type" v-model="creditNote.type">
              <option value="credit">{{ $t('app.credit.create.credit') }}</option>
              <option value="debit">{{ $t('app.credit.create.debit') }}</option>
            </select>
            <p class="form-field-help">{{ $t('app.credit.create.help_info.type') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="currency">
              {{ $t('app.credit.create.currency') }}
            </label>
            <p class="form-field-error" v-if="errors.currency != undefined">{{ errors.currency }}</p>
            <CurrencySelect v-model="creditNote.currency" />
            <p class="form-field-help">{{ $t('app.credit.create.help_info.currency') }}</p>
          </div>
          <div class="form-field-ctn">

            <label class="form-field-lbl" for="currency">
              {{ $t('app.credit.create.reason') }}
            </label>
            <input type="text" class="form-field" v-model="creditNote.reason" />
            <p class="form-field-help">{{ $t('app.credit.create.help_info.reason') }}</p>
          </div>
        </div>
    <div class="form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.credit.create.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.credit.create.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import currency from "currency.js";
import SettingsGroup from "../Settings/SettingsGroup.vue";
import CurrencySelect from "../../../components/app/Forms/CurrencySelect.vue";

export default {
  name: "CustomerCreate",
  components: {CurrencySelect, SettingsGroup},
  data() {
    return {
      creditNote: {
        amount: 0,
        currency: null,
        reason: null,
      },
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      errors: {
      }
    }
  },
  methods: {
    currency: function (value) {
      return currency(value, { fromCents: true });
    },
    send: function () {
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      const customerId = this.$route.params.customerId;
      axios.post('/app/customer/'+customerId+'/credit', this.creditNote).then(
          response => {
            this.sendingInProgress = false;
            this.success = true;
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
.form-field-error {
  @apply text-red-500 text-xs italic mb-2;
}

.form-field-ctn {
  @apply w-full md:w-1/2 px-3 mb-6 md:mb-0 pt-2;
}

.form-field-lbl {
  @apply block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2;
}

.form-field-input {
  @apply appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white;
}

.form-field-help {
  @apply text-gray-600 text-xs italic;
}

.form-field-submit-ctn {
  @apply mt-3;
}
</style>