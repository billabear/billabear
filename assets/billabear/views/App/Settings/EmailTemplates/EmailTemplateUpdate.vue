<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.email_template.update.title') }}</h1>

    <form @submit.prevent="send">
    <div class="m-5 card-body">
      <div class="form-field-ctn">
        <label class="form-field-lbl" for="name">
          {{ $t('app.settings.email_template.create.fields.name') }}
        </label>
        <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
        {{ emailTemplate.name }}
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="locale">
          {{ $t('app.settings.email_template.create.fields.locale') }}
        </label>
        <p class="form-field-error" v-if="errors.locale != undefined">{{ errors.locale }}</p>
        {{ emailTemplate.locale }}
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="brand">
          {{ $t('app.settings.email_template.create.fields.brand') }}
        </label>
        <p class="form-field-error" v-if="errors.name != undefined">{{ errors.brand }}</p>
        {{ emailTemplate.brand }}
      </div>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="use_emsp_template">
          {{ $t('app.settings.email_template.update.fields.use_emsp_template') }}
        </label>
        <p class="form-field-error" v-if="errors.use_emsp_template != undefined">{{ errors.use_emsp_template }}</p>
        <input type="checkbox" class="form-field" id="use_emsp_template" v-model="emailTemplate.use_emsp_template"  />
        <p class="form-field-help">{{ $t('app.settings.email_template.update.help_info.use_emsp_template') }}</p>
      </div>
      <div class="form-field-ctn" v-if="emailTemplate.use_emsp_template === false">
        <label class="form-field-lbl" for="subject">
          {{ $t('app.settings.email_template.update.fields.subject') }}
        </label>
        <p class="form-field-error" v-if="errors.subject != undefined">{{ errors.subject }}</p>
        <input type="text" class="form-field" id="use_emsp_template" v-model="emailTemplate.subject"  />
        <p class="form-field-help">{{ $t('app.settings.email_template.update.help_info.use_emsp_template') }}</p>
      </div>
      <div class="form-field-ctn" v-if="emailTemplate.use_emsp_template === false">
        <label class="form-field-lbl" for="subject">
          {{ $t('app.settings.email_template.update.fields.template_body') }}
        </label>
        <p class="form-field-error" v-if="errors.template_body != undefined">{{ errors.template_body }}</p>
        <textarea class="form-field" id="template_body" rows="9" cols="60" v-model="emailTemplate.template_body"></textarea>
        <p class="form-field-help">{{ $t('app.settings.email_template.update.help_info.template_body') }}</p>

      </div>
      <div class="form-field-ctn" v-if="emailTemplate.use_emsp_template === true">
        <label class="form-field-lbl" for="template_id">
          {{ $t('app.settings.email_template.update.fields.template_id') }}
        </label>
        <p class="form-field-error" v-if="errors.template_id != undefined">{{ errors.template_id }}</p>
        <input type="text" class="form-field" id="template_id" v-model="emailTemplate.template_id"  />
        <p class="form-field-help">{{ $t('app.settings.email_template.update.help_info.template_id') }}</p>
      </div>
      <p class="mt-4 form-field-help"><a :href="'https://docs.billabear.com/user/templates/email_variables?utm=' + origin + '&utm_campaign=billabear_doc_links&utm_medium=email_variables'">{{ $t('app.settings.email_template.update.help_info.variable_docs') }}</a> </p>

    </div>

    <div class="m-5 form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.settings.email_template.update.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.email_template.update.success_message') }}</p>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CustomerCreate",
  data() {
    return {
      allowedNames: [],
      origin: '',
      emailTemplate: {
        name: null,
        locale: null,
        subject: null,
        template_body: null,
        template_id: null,
        use_emsp_template: false,
      },
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      errors: {
      }
    }
  },
  mounted() {
    var id = this.$route.params.id
    this.origin = window.location.hostname;
    axios.get('/app/settings/email-template/'+id).then(response => {
      this.emailTemplate = response.data.email_template;
    })
  },
  methods: {
    send: function () {
      var id = this.$route.params.id
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      axios.post('/app/settings/email-template/'+id, this.emailTemplate).then(
          response => {
            this.sendingInProgress = false;
            this.success = true;
          }
      ).catch(error => {
        this.errors = error.response.data.errors;
        this.sendingInProgress = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>
.form-field-error {
  @apply text-red-500 text-xs italic mb-2;
}

.form-field-ctn {
  @apply w-full md:w-1/2 px-3 mb-6 md:mb-0 pt-2;
}

.form-field-lbl {
  @apply block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2;
}

.form-field-input {
  @apply appearance-none block w-full bg-gray-200 text-gray-700 border border-red-500 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white;
}

.form-field-help {
  @apply text-gray-600 text-xs italic;
}

.form-field-submit-ctn {
  @apply mt-3;
}
</style>