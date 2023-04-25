<template>
  <div>
    <LoadingScreen :ready="ready">
      <h1 class="page-title">{{ $t('app.settings.template.update.title', {name: template.template.name}) }}</h1>

      <div class="form-field-ctn">
        <label class="form-field-lbl" for="content">
          {{ $t('app.settings.template.update.reference') }}
        </label>
        <p class="form-field-error" v-if="errors.content != undefined">{{ errors.content }}</p>
        <textarea v-model="template.content"></textarea>
        <p class="form-field-help">{{ $t('app.settings.template.update.help_info.tenplate') }}</p>
      </div>

      <div class="mt-5">
        <SubmitButton :in-progress="sendingUpdate" @click="save">{{ $t('app.settings.template.update.save') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "TemplateView",
  data() {
    return {
      ready: false,
      sendingUpdate: false,
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