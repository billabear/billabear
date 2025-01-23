<template>
  <div>
    <h1 class="page-title">{{ $t('app.tax_type.update.title') }}</h1>
    <LoadingScreen :ready="loaded">

      <div class="">
        <div class="card-body">

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.tax_type.update.tax_type.fields.name') }}
            </label>
            <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
            <input type="text" class="form-field" v-model="type.name" />
            <p class="form-field-help">{{ $t('app.tax_type.update.tax_type.help_info.name') }}</p>
          </div>

          <div class="form-field-ctn">

            <label class="form-field-lbl" for="name">
              {{ $t('app.tax_type.update.tax_type.fields.vat_sense_type') }}
            </label>
            <p class="form-field-error" v-if="errors.vatSenseType != undefined">{{ errors.vatSenseType }}</p>
            <select class="form-field" v-model="type.vat_sense_type">
              <option v-for="type in types" :value="type">{{type}}</option>
            </select>
            <p class="form-field-help">{{ $t('app.tax_type.update.tax_type.help_info.vat_sense_type') }}</p>
          </div>
        </div>
      </div>

      <div class="mt-3">
        <SubmitButton :in-progress="sending" @click="send">{{ $t('app.tax_type.update.update_button') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import {Toggle} from "flowbite-vue";

export default {
  name: "TaxTypeUpdate",
  components: {Toggle},
  data() {
    return {
      errors: {},
      type: {
        vat_sense_type: null,
      },
      types: [
          null,
          "accommodation",
          "admission to cultural events",
          "admission to entertainment eve",
          "admission to sporting events",
          "electronic services",
          "advertising",
          "agricultural supplies",
          "baby foodstuffs",
          "bikes",
          "books",
          "childrens clothing",
          "domestic fuel",
          "domestic services",
          "ebooks",
          "foodstuffs",
          "hotels",
          "medical",
          "newspapers",
          "passenger transport",
          "pharmaceuticals",
          "property renovations",
          "restaurants",
          "social housing",
          "water",
          "wine"
      ],
      sending: false,
      loaded: false,
    }
  },
  mounted() {
    const taxTypeId = this.$route.params.id;
      axios.get("/app/tax/type/"+taxTypeId+"/update").then((response) => {
        this.type = response.data;
        this.loaded = true;
      });
  },
  methods: {
    send: function () {
      this.sending = true;
      this.errors = {};
      const taxTypeId = this.$route.params.id;
      axios.post("/app/tax/type/"+taxTypeId+"/update", this.type).then(response => {
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
