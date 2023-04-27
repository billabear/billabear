<template>
  <div>
    <LoadingScreen :ready="ready">
      <h1 class="page-title">{{ $t('app.settings.pdf_template.update.title', {name: template.template.name}) }}</h1>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="content">
          {{ $t('app.settings.pdf_template.update.template') }}
        </label>
        <p class="form-field-error" v-if="errors.content != undefined">{{ errors.content }}</p>
        <textarea class="form-field" rows="10" cols="80" v-model="template.content"></textarea>
        <p class="form-field-help">{{ $t('app.settings.pdf_template.update.help_info.template') }}</p>
      </div>

      <div class="mt-5">
        <SubmitButton :in-progress="downloadInProgress" @click="download" button-class="btn--secondary mr-4">{{ $t('app.settings.pdf_template.update.download') }}</SubmitButton>
        <SubmitButton :in-progress="sendingUpdate" @click="save">{{ $t('app.settings.pdf_template.update.save') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "PdfTemplateUpdate",
  data() {
    return {
      ready: false,
      sendingUpdate: false,
      downloadInProgress: false,
      template: {template: {}},
      errors: {}
    }
  },
  mounted() {
    const templateId = this.$route.params.id;
    axios.get('/app/settings/template/'+templateId).then(response => {
      this.template = response.data;
      this.ready = true;
    }).catch(error => {

    })
  },
  methods: {
    download: function () {
      const templateId = this.$route.params.id;
      this.downloadInProgress = true;
      axios.get('/app/settings/template/'+templateId+'/receipt-download', {  responseType: 'blob'}).then(response => {
        var fileDownload = require('js-file-download');
        fileDownload(response.data, 'report.pdf');
        this.downloadInProgress = false;
      })
    },
    save: function () {
      const templateId = this.$route.params.id;
      this.sendingUpdate = true;
      axios.post('/app/settings/template/'+templateId, {content: this.template.content}).then(response => {
        this.sendingUpdate = false;
      })
    }
  }
}
</script>

<style scoped>

</style>