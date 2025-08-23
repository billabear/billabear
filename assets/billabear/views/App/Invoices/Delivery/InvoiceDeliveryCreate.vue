<template>
  <div>
    <PageTitle>{{ $t('app.invoices.delivery.create.title') }}</PageTitle>
    <form @submit.prevent="save">
      <div class="card-body">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.invoices.delivery.create.fields.method') }}
          </label>
          <select class="form-field" v-model="invoice_delivery.type">
            <option value="email">E-Mail</option>
            <option value="sftp">SFTP</option>
            <option value="webhook">webhook</option>
          </select>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.invoices.delivery.create.fields.format') }}
          </label>
          <select class="form-field" v-model="invoice_delivery.format">
            <option v-for="format in formatters" :value="format">{{ $t(format) }}</option>
          </select>
        </div>
      </div>

      <div class="card-body mt-3" v-if="invoice_delivery.type === 'email'">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="email">
            {{ $t('app.invoices.delivery.create.fields.email.email') }}
          </label>
          <input type="text" class="form-field" v-model="email" />
          <p class="form-field-help">{{ $t('app.invoices.delivery.create.fields.email.help_info') }}</p>
        </div>
      </div>

      <div class="card-body mt-3" v-if="invoice_delivery.type === 'sftp'">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="username">
            {{ $t('app.invoices.delivery.create.fields.sftp.username') }}
          </label>
          <input type="text" class="form-field" v-model="invoice_delivery.sftp_user" />
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="password">
            {{ $t('app.invoices.delivery.create.fields.sftp.password') }}
          </label>
          <input type="text" class="form-field" v-model="invoice_delivery.sftp_password" />
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="hostname">
            {{ $t('app.invoices.delivery.create.fields.sftp.hostname') }}
          </label>
          <input type="text" class="form-field" v-model="invoice_delivery.sftp_host" />
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="port">
            {{ $t('app.invoices.delivery.create.fields.sftp.port') }}
          </label>
          <input type="number" class="form-field" v-model="invoice_delivery.sftp_port" />
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="directory">
            {{ $t('app.invoices.delivery.create.fields.sftp.directory') }}
          </label>
          <input type="text" class="form-field" v-model="invoice_delivery.sftp_dir" />
        </div>
      </div>

      <div class="card-body mt-3" v-if="invoice_delivery.type === 'webhook'">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="method">
            {{ $t('app.invoices.delivery.create.fields.webhook.method') }}
          </label>
          <select class="form-field" v-model="invoice_delivery.webhook_method">
            <option value="POST">POST</option>
            <option value="PUT">PUT</option>
          </select>
        </div>
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="url">
            {{ $t('app.invoices.delivery.create.fields.webhook.url') }}
          </label>
          <input type="text" class="form-field" v-model="invoice_delivery.webhook_url" />
        </div>

      </div>
      <div class="mt-3">
        <SubmitButton :in-progress="sending" class="btn--main" @click="save">
          {{ $t('app.invoices.delivery.create.save') }}
        </SubmitButton>
      </div>

    </form>
  </div>
</template>

<script>
import PageTitle from "../../../../components/app/Ui/Typography/PageTitle.vue";
import axios from "axios";

export default {
  name: "InvoiceDeliveryCreate",
  components: {PageTitle},
  data() {
    return {
      invoice_delivery: {
        method: null,
        format: null,
        sftp_user: null,
        sftp_password: null,
        sftp_host: null,
        sftp_port: null,
        sftp_dir: null,
        webhook_url: null,
        webhook_method: null,
      },
      formatters: [],
      errors: {},
      sending: false,
    }
  },
  mounted() {
    axios.get("/app/invoice-delivery").then(response => {
      this.formatters = response.data.formatters;
    })
  },
  methods: {
    save: function() {
      this.sending = true;
      const customerId = this.$route.params.customerId;
      axios.post("/app/customer/"+customerId+"/invoice-delivery", this.invoice_delivery).then(resposne => {
        this.$router.push({name: 'app.customer.view', params: {id: customerId}})
      })
    }
  }
}
</script>

<style scoped>

</style>
