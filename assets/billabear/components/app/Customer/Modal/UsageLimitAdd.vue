<template>
  <div>
    <h1 class="page-title">{{ $t('app.usage_limit.create.title') }}</h1>

    <form @submit.prevent="send">

      <div class="mt-3 ">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="amount">
            {{ $t('app.usage_limit.create.fields.amount') }}
          </label>
          <p class="form-field-error" v-if="errors.amount != undefined">{{ errors.amount }}</p>
          <CurrencyInput v-model="limit.amount" />
          <p class="form-field-help">{{ $t('app.usage_limit.create.help_info.amount') }}</p>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="action">
            {{ $t('app.usage_limit.create.fields.action') }}
          </label>
          <select class="form-field">
            <option value="1000">
              {{ $t('app.usage_limit.create.actions.warn') }}
            </option>
            <option value="9999">
              {{ $t('app.usage_limit.create.actions.disable') }}
            </option>
          </select>
          <p class="form-field-help">{{ $t('app.usage_limit.create.help_info.action') }}</p>
        </div>
        <SubmitButton @click="send" :in-progress="sendingInProgress" class="mt-5 btn--main" >{{ $t('app.usage_limit.create.submit') }}</SubmitButton>
      </div>
    </form>
  </div>
</template>

<script>
import CurrencyInput from "../../Forms/CurrencyInput.vue";
import axios from "axios";

export default {
  name: "UsageLimitAdd" ,
  components: {CurrencyInput},
  props: {
    customer: Object,
    limits: Array,
  },
  data() {
    return {
      limit: {
        amount: 0,
        warn_level: 1000,
      },
      errors: {},
      sendingInProgress: false
    }
  },
  methods: {
      send: function () {
        this.errors = {};
        this.sendingInProgress = true;

        axios.post("/app/customer/"+this.customer.id+"/usage-limit", this.limit).then(response => {
          this.limits.push(this.limit);
          this.$emit('close-modal');
        }).catch(error => {
          if (error.response !== undefined) {
            this.errors = error.response.data.errors;
          }
          this.sendingInProgress = false;
        })
      }
  },
}
</script>

<style scoped>

</style>
