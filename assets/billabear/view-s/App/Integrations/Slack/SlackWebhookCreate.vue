<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">
      {{ $t('app.system.integrations.slack.webhooks.create.title') }}
    </h1>

    <div>
      <form @submit.prevent="send">
        <div class="">
          <div class="card-body">
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="name">
                {{
                  $t(
                    'app.system.integrations.slack.webhooks.create.fields.name'
                  )
                }}
              </label>
              <p class="form-field-error" v-if="errors.name != undefined">
                {{ errors.name }}
              </p>
              <input
                type="text"
                class="form-field-input"
                id="name"
                v-model="webhook.name"
              />
              <p class="form-field-help">
                {{
                  $t(
                    'app.system.integrations.slack.webhooks.create.help_info.name'
                  )
                }}
              </p>
            </div>

            <div class="form-field-ctn mt-4">
              <label class="form-field-lbl" for="webhook">
                {{
                  $t(
                    'app.system.integrations.slack.webhooks.create.fields.webhook'
                  )
                }}
              </label>
              <p class="form-field-error" v-if="errors.webhook != undefined">
                {{ errors.webhook }}
              </p>
              <input
                type="text"
                class="form-field-input"
                id="webhook"
                v-model="webhook.webhook"
              />
              <p class="form-field-help">
                {{
                  $t(
                    'app.system.integrations.slack.webhooks.create.help_info.webhook'
                  )
                }}
              </p>
            </div>
          </div>
        </div>
        <SubmitButton class="mt-3" @click="save" :in-progress="inProgress">{{
          $t('app.system.integrations.slack.webhooks.create.save_btn')
        }}</SubmitButton>
      </form>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useForm } from '../../../composables/useForm'

// Router
const router = useRouter()

// Initial form data
const initialWebhookData = {
  name: '',
  webhook: '',
}

// Form handling with useForm composable
const {
  formData: webhook,
  isSubmitting: inProgress,
  errors,
  submitForm,
} = useForm(initialWebhookData)

// Form submission
const save = async () => {
  try {
    await submitForm('/app/integrations/slack/webhook/create', {
      onSuccess: () => {
        router.push({ name: 'app.system.integrations.slack.webhook' })
      },
    })
  } catch (error) {
    // Error handling is managed by the useForm composable
  }
}
</script>

<style scoped></style>
