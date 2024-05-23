<template>
  <div>
    <h1 class="page-title">{{ $t('app.economic_area.create.title') }}</h1>

    <div class="card-body m-5">

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.economic_area.create.fields.name') }}
        </label>
        <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
        <input type="text" class="form-field" v-model="area.name" />
        <p class="form-field-help">{{ $t('app.economic_area.create.help_info.name') }}</p>
      </div>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.economic_area.create.fields.threshold') }}
        </label>
        <p class="form-field-error" v-if="errors.threshold != undefined">{{ errors.threshold }}</p>
        <input type="number" class="form-field" v-model="area.threshold" />
        <p class="form-field-help">{{ $t('app.economic_area.create.help_info.threshold') }}</p>
      </div>
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.economic_area.create.fields.currency') }}
        </label>
        <p class="form-field-error" v-if="errors.currency != undefined">{{ errors.currency }}</p>
        <CurrencySelect v-model="area.currency" />
      </div>
    </div>
    <div class="mt-5 ml-5">
      <SubmitButton :in-progress="sending" @click="send">{{ $t('app.economic_area.create.create_button') }}</SubmitButton>
    </div>
  </div>
</template>

<script>
import CurrencySelect from "../../../components/app/Forms/CurrencySelect.vue";
import axios from "axios";

export default {
  name: "EconomicAreaCreate",
  components: {CurrencySelect},
  data() {
    return {
      sending: false,
      area: {},
      errors: {}
    }
  },
  methods: {
    send: function () {
      this.errors = {};
      this.sending = true;
      axios.post("/app/economic-area", this.area).then(response => {
        this.$router.push({'name': 'app.finance.economic_area.list'})
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.sending = false;
      })
    }
  }
}
</script>

<style scoped>

</style>
