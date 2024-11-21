<template>
  <div>
    <LoadingMessage v-if="!ready">
      {{ $t("app.invoices.download.loading_message") }}
    </LoadingMessage>
    <div v-else>
      <h2 class="text-3xl font-bold mb-3">{{ $t("app.invoices.download.format") }}</h2>

      <select class="form-field" v-model="format">
        <option v-for="format in formatters" :value="format">{{ $t(format) }}</option>
      </select>

      <SubmitButton :in-progress="downloadInProgress" class="btn--main block mt-3" @click="downloadInvoice">
        {{ $t("app.invoices.download.download") }}
      </SubmitButton>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import fileDownload from "js-file-download";

export default {
  name: "DownloadInvoice",
  props: {
    invoice: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      ready: false,
      formatters: [],
      format: null,
      downloadInProgress: false,
    };
  },
  mounted() {
    axios.get('/app/invoice-delivery').then(response => {
      this.formatters = response.data.formatters;
      this.format = response.data.formatters[0];
      this.ready = true;
    });
  },
  methods: {
    downloadInvoice: function () {
      this.showError = false;
      axios.get('/app/invoice/'+this.invoice.id+'/download?format='+this.format, {  responseType: 'blob'}).then(response => {
        console.log(response);
        var fileDownload = require('js-file-download');
        const contentDisposition = response.headers['content-disposition'];
        let filename = 'invoice-'+this.invoice.id+'.pdf';
        console.log(contentDisposition);
        if (contentDisposition) {const filenameRegex = /filename=(.*)/;
          const filenameMatch = filenameRegex.exec(contentDisposition);
          filename = filenameMatch[1];
        }
        fileDownload(response.data, filename);
        this.downloadInProgress = false;
      }).catch(error => {
        console.log(error);
        var that = this;
        let errorString = async function getString() {
          const str = await error.response.data.text();
          const errorString = JSON.parse(str);
          return str;
        }
        errorString();

        this.showError= true;
        this.downloadInProgress = false;
      })
    },
  }
}
</script>

<style scoped>

</style>
