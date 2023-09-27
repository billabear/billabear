<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">{{ $t('app.feature.create.title') }}</h1>

    <form @submit.prevent="send">
      <div class="p-5">

        <div class="card-body mb-5">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="name">
              {{ $t('app.feature.create.fields.name') }}
            </label>
            <p class="form-field-error" v-if="errors.name != undefined">{{ errors.name }}</p>
            <input type="text" class="form-field-input" id="name" v-model="feature.name" />
            <p class="form-field-help">{{ $t('app.feature.create.help_info.name') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="code">
              {{ $t('app.feature.create.fields.code') }}
            </label>
            <p class="form-field-error" v-if="errors.code != undefined">{{ errors.code }}</p>
            <input type="text" class="form-field-input" id="code" v-model="feature.code" />
            <p class="form-field-help">{{ $t('app.feature.create.help_info.code') }}</p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="description">
              {{ $t('app.feature.create.fields.description') }}
            </label>
            <p class="form-field-error" v-if="errors.description != undefined">{{ errors.description }}</p>
            <input type="text" class="form-field-input" id="description" v-model="feature.description" />
            <p class="form-field-help">{{ $t('app.feature.create.help_info.description') }}</p>
          </div>

        </div>


    <div class="form-field-submit-ctn">
      <SubmitButton :in-progress="sendingInProgress">{{ $t('app.feature.create.submit_btn') }}</SubmitButton>
    </div>
    <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.feature.create.success_message') }}</p>
      </div>
    </form>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "FeatureCreate",
  data() {
    return {
      feature: {
        name: null,
        code: null,
        description: null
      },
      sendingInProgress: false,
      showAdvance: false,
      success: false,
      errors: {
      }
    }
  },
  methods: {
    send: function () {
      this.sendingInProgress = true;
      this.success = false;
      this.errors = {};
      axios.post('/app/feature', this.feature).then(
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
</style>