<template>
  <div>
    <LoadingScreen ready="ready">
      <h1 class="page-title">{{ $t('app.settings.pdf_template.generator_settings.title') }}</h1>

      <div class="m-5">
        <div class="card-body">

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="content">
              {{ $t('app.settings.pdf_template.generator_settings.generator') }}
            </label>
            <p class="form-field-error" v-if="errors.generator != undefined">{{ errors.generator }}</p>
            <select v-model="settings.generator">
              <option value="mpdf">MPDF</option>
              <option value="wkhtmltopdf">wkhtmltopdf</option>
              <option value="docraptor">DocRaptor</option>
            </select>
            <p class="form-field-help">{{ $t('app.settings.pdf_template.generator_settings.help_info.generator') }}</p>
          </div>

          <div class="form-field-ctn mt-3" v-if="settings.generator == 'mpdf'">
            <label class="form-field-lbl" for="content">
              {{ $t('app.settings.pdf_template.generator_settings.tmp_dir') }}
            </label>
            <p class="form-field-error" v-if="errors.tmp_dir != undefined">{{ errors.tmp_dir }}</p>
            <input type="text" class="form-field" v-model="settings.tmp_dir" />
            <p class="form-field-help">{{ $t('app.settings.pdf_template.generator_settings.help_info.tmp_dir') }}</p>
          </div>

          <div class="form-field-ctn mt-3" v-if="settings.generator == 'wkhtmltopdf'">
            <label class="form-field-lbl" for="content">
              {{ $t('app.settings.pdf_template.generator_settings.bin') }}
            </label>
            <p class="form-field-error" v-if="errors.bin != undefined">{{ errors.bin }}</p>
            <input type="text" class="form-field" v-model="settings.bin" />
            <p class="form-field-help">{{ $t('app.settings.pdf_template.generator_settings.help_info.bin') }}</p>
          </div>

          <div class="form-field-ctn mt-3" v-if="settings.generator == 'docraptor'">
            <label class="form-field-lbl" for="content">
              {{ $t('app.settings.pdf_template.generator_settings.api_key') }}
            </label>
            <p class="form-field-error" v-if="errors.api_key != undefined">{{ errors.api_key }}</p>
            <input type="text" class="form-field" v-model="settings.api_key" />
            <p class="form-field-help">{{ $t('app.settings.pdf_template.generator_settings.help_info.api_key') }}</p>
          </div>
        </div>
      </div>
      <div class="m-5">
        <SubmitButton :in-progress="sending" @click="send">{{$t('app.settings.pdf_template.generator_settings.submit') }}</SubmitButton>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.notification_settings.update.success_message') }}</p>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import {Input, Select} from "flowbite-vue";

export default {
  name: "PdfGeneratorSettings",
  components: {Input, Select},
  data() {
    return {
      ready: false,
      settings: {},
      errors: {},
      sending: false,
      success: false,
    }
  },
  mounted() {
    axios.get("/app/settings/pdf-generator").then(response => {
      this.settings = response.data;
      this.ready = true;
    })
  },
  methods: {
    send: function (){
      this.errors = {};
      this.sending = true;
      axios.post("/app/settings/pdf-generator", this.settings).then(response => {
        this.sending = false;
        this.success = true;
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.sending = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>

</style>