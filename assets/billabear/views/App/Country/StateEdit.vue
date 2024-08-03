<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.state.edit.title') }}</h1>

    <LoadingScreen :ready="loaded">
      <div class="card-body">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.state.edit.state.fields.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field" v-model="state.name" />
          <p class="form-field-help">{{ $t('app.state.edit.state.help_info.name') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="code">
            {{ $t('app.state.edit.state.fields.code') }}
          </label>
          <p class="form-field-error" v-if="errors.code != undefined">{{ errors.code }}</p>
          <input type="text" class="form-field" v-model="state.code" />
          <p class="form-field-help">{{ $t('app.state.edit.state.help_info.code') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="collecting">
            {{ $t('app.state.edit.state.fields.collecting') }}
          </label>
          <p class="form-field-error" v-if="errors.collecting != undefined">{{ errors.collecting }}</p>
          <Toggle v-model="state.collecting" />
          <p class="form-field-help">{{ $t('app.state.edit.state.help_info.collecting') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="threshold">
            {{ $t('app.state.edit.state.fields.threshold') }}
          </label>
          <p class="form-field-error" v-if="errors.code != undefined">{{ errors.threshold }}</p>
          <CurrencyInput v-model="state.threshold" />
          <p class="form-field-help">{{ $t('app.state.edit.state.help_info.threshold') }}</p>
        </div>
      </div>
      <SubmitButton class="mt-3" :in-progress="sending" @click="sendCreate">{{ $t('app.state.edit.update_button') }}</SubmitButton>
    </LoadingScreen>
  </div>
</template>

<script>
import CurrencyInput from "../../../components/app/Forms/CurrencyInput.vue";
import axios from "axios";
import {Toggle} from "flowbite-vue";

export default {
  name: "StateCreate",
  components: {Toggle, CurrencyInput},
  data() {
    return {
      state: {
        name: '',
        code: '',
        collecting: false,
        threshold: 0,
        country: ''
      },
      errors: {},
      sending: false,
      loaded: false,
    }
  },
  mounted() {
    const countryId = this.$route.params.countryId;
    const stateId = this.$route.params.stateId;
    axios.get(`/app/country/${countryId}/state/${stateId}/view`, this.$route.params.id).then(response => {
      this.state = response.data.state;
      this.loaded = true;
    })
  },
  methods: {
    sendCreate: function () {
      var id = this.$route.params.id;
      this.sending = true;
      this.errors = {};
      const countryId = this.$route.params.countryId;
      const stateId = this.$route.params.stateId;
      axios.post(`/app/country/${countryId}/state/${stateId}/edit`, this.state).then(response => {
        this.$router.push({'name': 'app.finance.state.view', params: {countryId: id,stateId: response.data.id}})
      }).catch(error => {
        if (error.response != undefined) {
          this.errors = error.response.data.errors;
          this.sending = false;
        }
      })
    }
  }
}
</script>

<style scoped>

</style>
