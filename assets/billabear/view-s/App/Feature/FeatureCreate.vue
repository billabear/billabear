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
            <p class="form-field-error" v-if="errors.name != undefined">
              {{ errors.name }}
            </p>
            <input
              type="text"
              class="form-field-input"
              id="name"
              v-model="feature.name"
            />
            <p class="form-field-help">
              {{ $t('app.feature.create.help_info.name') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="code">
              {{ $t('app.feature.create.fields.code') }}
            </label>

            <input
              type="text"
              class="form-field-input"
              id="code"
              v-model="feature.code"
            />
            <p class="form-field-help">
              {{ $t('app.feature.create.help_info.code') }}
            </p>
          </div>
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="description">
              {{ $t('app.feature.create.fields.description') }}
            </label>
            <p class="form-field-error" v-if="errors.description != undefined">
              {{ errors.description }}
            </p>
            <input
              type="text"
              class="form-field-input"
              id="description"
              v-model="feature.description"
            />
            <p class="form-field-help">
              {{ $t('app.feature.create.help_info.description') }}
            </p>
          </div>
        </div>

        <p class="text-green-500 font-weight-bold" v-if="success">
          {{ $t('app.feature.create.success_message') }}
        </p>
        <div class="form-field-submit-ctn">
          <SubmitButton :in-progress="sendingInProgress">{{
            $t('app.feature.create.submit_btn')
          }}</SubmitButton>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { useForm } from '../../../composables/useForm'

// Initialize form with default feature data
const initialData = {
  name: null,
  code: null,
  description: null,
}

// Use form composable for form handling
const {
  formData: feature,
  isSubmitting: sendingInProgress,
  success,
  errors,
  submitForm,
} = useForm(initialData)

// Form submission handler
const send = async () => {
  try {
    await submitForm('/app/feature')
  } catch (error) {
    // Error handling is managed by the composable
  }
}
</script>

<style scoped></style>
