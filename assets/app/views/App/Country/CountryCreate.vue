<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.country.create.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="p-5">
        <div class="card-body">

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.create.country.fields.name') }}
            </label>
            <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
            <input type="text" class="form-field" v-model="country.name" />
            <p class="form-field-help">{{ $t('app.country.create.country.help_info.name') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.create.country.fields.iso_code') }}
            </label>
            <p class="form-field-error" v-if="errors.isoCode != undefined">{{ errors.isoCode }}</p>
            <input type="text" class="form-field" v-model="country.iso_code" />
            <p class="form-field-help">{{ $t('app.country.create.country.help_info.iso_code') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.create.country.fields.currency') }}
            </label>
            <p class="form-field-error" v-if="errors.currency != undefined">{{ errors.currency }}</p>
            <CurrencySelect v-model="country.currency" />
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.country.create.country.fields.threshold') }}
            </label>
            <p class="form-field-error" v-if="errors.threshold != undefined">{{ errors.threshold }}</p>
            <input type="number" class="form-field" v-model="country.threshold" />
            <p class="form-field-help">{{ $t('app.country.create.country.help_info.threshold') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="in_eu">
              {{ $t('app.country.create.country.fields.in_eu') }}
            </label>
            <p class="form-field-error" v-if="errors.inEu != undefined">{{ errors.inEu }}</p>
            <input type="checkbox" class="form-field" v-model="country.in_eu" />
            <p class="form-field-help">{{ $t('app.country.create.country.help_info.in_eu') }}</p>
          </div>
        </div>
      </div>
      <div class="mt-5 ml-5">
        <SubmitButton :in-progress="sending" @click="send">{{ $t('app.country.create.create_button') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import CurrencySelect from "../../../components/app/Forms/CurrencySelect.vue";
import axios from "axios";

export default {
  name: "CountryCreate",
  components: {CurrencySelect},
  data() {
    return {
      ready: true,
      sending: false,
      errors: {
      },
      country: {
        name: null,
        iso_code: null,
        currency: null,
        threshold: 0,
        in_eu: false,
      }
    }
  },
  methods: {
    send: function () {
      this.errors = {};
      this.sending = true;
      axios.post("/app/country", this.country).then(response => {
        this.$router.push({'name': 'app.system.country.view', params: {id: response.data.id}})
        this.sending = false;
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