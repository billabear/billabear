<template>
  <div>
    <LoadingScreen :ready="ready">
      <h1 class="page-title">
        {{ $t('app.settings.pdf_template.create.title') }}
      </h1>

      <div class="card-body m-5">
        <div class="form-field-ctn">
          <label class="form-field-lbl" for="locale">
            {{ $t('app.settings.pdf_template.create.locale') }}
          </label>
          <p class="form-field-error" v-if="errors.locale != undefined">
            {{ errors.locale }}
          </p>
          <input
            type="text"
            class="form-field"
            id="locale"
            v-model="template.locale"
          />
          <p class="form-field-help">
            {{ $t('app.settings.pdf_template.create.help_info.locale') }}
          </p>
          <label class="form-field-lbl" for="type">
            {{ $t('app.settings.pdf_template.create.type') }}
          </label>
          <p class="form-field-error" v-if="errors.type != undefined">
            {{ errors.type }}
          </p>
          <select v-model="template.type" class="form-field">
            <option v-for="name in allowedNames" :value="name">
              {{ name }}
            </option>
          </select>
          <p class="form-field-help">
            {{ $t('app.settings.pdf_template.create.help_info.type') }}
          </p>

          <label class="form-field-lbl" for="type">
            {{ $t('app.settings.pdf_template.create.brand') }}
          </label>
          <p class="form-field-error" v-if="errors.brand != undefined">
            {{ errors.brand }}
          </p>
          <select v-model="template.brand" class="form-field">
            <option v-for="(brand, code) in brands" :value="brand.code">
              {{ brand.name }}
            </option>
          </select>
          <p class="form-field-help">
            {{ $t('app.settings.pdf_template.create.help_info.brand') }}
          </p>
          <label class="form-field-lbl" for="content">
            {{ $t('app.settings.pdf_template.create.template') }}
          </label>
          <p class="form-field-error" v-if="errors.template != undefined">
            {{ errors.template }}
          </p>
          <textarea
            class="form-field"
            rows="10"
            cols="80"
            v-model="template.template"
          ></textarea>
          <p class="form-field-help">
            {{ $t('app.settings.pdf_template.create.help_info.template') }}
          </p>
          <p class="mt-4 form-field-help">
            <a
              :href="
                'https://docs.billabear.com/user/templates/pdf_variables?utm=' +
                origin +
                '&utm_campaign=billabear_doc_links&utm_medium=email_variables'
              "
              >{{
                $t('app.settings.pdf_template.update.help_info.variable_docs')
              }}</a
            >
          </p>
        </div>

        <div class="mt-5">
          <SubmitButton :in-progress="sendingInProgress" @click="save">{{
            $t('app.settings.pdf_template.create.save')
          }}</SubmitButton>
        </div>
      </div>
      <VueFinalModal
        v-model:value="options.modelValue"
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
import { VueFinalModal } from 'vue-final-modal'
import axios from 'axios'

export default {
  name: 'PdfTemplateCreate.vue',
  components: { VueFinalModal },
  data() {
    return {
      ready: false,
      origin: '',
      sendingInProgress: false,
      downloadInProgress: false,
      template: {},
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
      allowedNames: ['receipt', 'invoice', 'quote'],
      brands: [],
    }
  },
  mounted() {
    const templateId = this.$route.params.id
    this.origin = window.location.hostname
    this.ready = true
    axios
      .get('/app/settings/template/create')
      .then((response) => {
        this.ready = true
        this.brands = response.data.brands
      })
      .catch((error) => {})
  },
  methods: {
    save: function () {
      this.sendingInProgress = true
      this.errors = {}
      axios
        .post('/app/settings/template/create', this.template)
        .then((response) => {
          this.$router.push({ name: 'app.settings.pdf_template.list' })
        })
        .catch((error) => {
          this.errors = error.response.data.errors
          this.sendingInProgress = false
          this.success = false
        })
    },
  },
}
</script>

<style scoped></style>
