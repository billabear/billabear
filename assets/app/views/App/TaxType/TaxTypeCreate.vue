<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.tax_type.create.title') }}</h1>
    <div class="p-5">
      <div class="card-body">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.tax_type.create.tax_type.fields.name') }}
          </label>
          <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
          <input type="text" class="form-field" v-model="type.name" />
          <p class="form-field-help">{{ $t('app.tax_type.create.tax_type.help_info.name') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.tax_type.create.tax_type.fields.physical') }}
          </label>
          <p class="form-field-error" v-if="errors.physical != undefined">{{ errors.physical }}</p>

          <toggle v-model="type.physical" label="" />
          <p class="form-field-help">{{ $t('app.tax_type.create.tax_type.help_info.physical') }}</p>
        </div>
      </div>
    </div>

    <div class="mt-5 ml-5">
      <SubmitButton :in-progress="sending" @click="send">{{ $t('app.tax_type.create.create_button') }}</SubmitButton>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import {Toggle} from "flowbite-vue";
export default {
  name: "TaxTypeCreate",
  components: {Toggle},
  data() {
    return {
      errors: {},
      type: {},
      sending: false,
    }
  },
  methods: {
    send: function () {
      this.sending = true;
      this.errors = {};
      axios.post("/app/tax/type", this.type).then(response => {
        this.$router.push({'name': 'app.system.tax_type.list'})
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