<template>
  <div>
    <LoadingScreen :ready="ready">
      <h1 class="page-title">{{ $t('app.settings.pdf_template.update.title', {name: template.template.name}) }}</h1>

      <div class="card-body m-5">
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="content">
          {{ $t('app.settings.pdf_template.update.template') }}
        </label>
        <p class="form-field-error" v-if="errors.content != undefined">{{ errors.content }}</p>
        <textarea class="form-field" rows="10" cols="80" v-model="template.content"></textarea>
        <p class="form-field-help">{{ $t('app.settings.pdf_template.update.help_info.template') }}</p>
        <p class="mt-4 form-field-help"><a :href="'https://docs.billabear.com/user/templates/pdf_variables?utm=' + origin + '&utm_campaign=billabear_doc_links&utm_medium=email_variables'">{{ $t('app.settings.pdf_template.update.help_info.variable_docs') }}</a> </p>

      </div>

      <div class="mt-5">
        <SubmitButton :in-progress="downloadInProgress" @click="download" button-class="btn--secondary mr-4">{{ $t('app.settings.pdf_template.update.download') }}</SubmitButton>
        <SubmitButton :in-progress="sendingUpdate" @click="save">{{ $t('app.settings.pdf_template.update.save') }}</SubmitButton>
      </div></div>
      <VueFinalModal
          v-model="options.modelValue"
          :teleport-to="options.teleportTo"
          :display-directive="options.displayDirective"
          :hide-overlay="options.hideOverlay"
          :overlay-transition="options.overlayTransition"
          :content-transition="options.contentTransition"
          :click-to-close="options.clickToClose"
          :esc-to-close="options.escToClose"
          :background="options.background"
          :lock-scroll="options.lockScroll"
          :swipe-to-close="options.swipeToClose"
          class="flex justify-center items-center"
          content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2"
      >
        {{ templateError }}
      </VueFinalModal>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import {VueFinalModal} from "vue-final-modal";

export default {
  name: "PdfTemplateUpdate",
  components: {VueFinalModal},
  data() {
    return {
      ready: false,
      origin: '',
      sendingUpdate: false,
      downloadInProgress: false,
      template: {template: {}},
      errors: {},
      templateError: null,
      options: {
        teleportTo: 'body',
        modelValue: false,
        displayDirective: 'if',
        hideOverlay: false,
        overlayTransition: 'vfm-fade',
        contentTransition: 'vfm-fade',
        clickToClose: true,
        escToClose: true,
        background: 'non-interactive',
        lockScroll: true,
        swipeToClose: 'none',
      },
    }
  },
  mounted() {
    const templateId = this.$route.params.id;
    this.origin = window.location.hostname;
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
      axios.get('/app/settings/template/'+templateId+'/'+this.template.template.name+'-download', {  responseType: 'blob'}).then(response => {
        var fileDownload = require('js-file-download');
        fileDownload(response.data, 'example.pdf');
        this.downloadInProgress = false;
      }).catch(error => {
        var that = this;
         let errorString = async function getString() {
           const str = await error.response.data.text();
           const errorString = JSON.parse(str);
           that.templateError = errorString.raw_message;
           that.options.modelValue = true;
           return str;
         }
        errorString();

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