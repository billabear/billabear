<template>
  <div>
    <h1 class="page-title">{{ $t('app.feature.create.title') }}</h1>

    <form @submit.prevent="send">
    <div class="mt-3 card-body">
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