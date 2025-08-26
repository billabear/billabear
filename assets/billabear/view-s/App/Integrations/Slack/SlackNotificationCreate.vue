<template>
  <div>
    <h1 class="ml-5 mt-5 page-title">
      {{ $t('app.system.integrations.slack.notifications.create.title') }}
    </h1>

    <div>
      <form @submit.prevent="send">
        <div class="">
          <div class="card-body">
            <div class="form-field-ctn">
              <label class="form-field-lbl" for="name">
                {{
                  $t(
                    'app.system.integrations.slack.notifications.create.fields.event'
                  )
                }}
              </label>
              <p class="form-field-error" v-if="errors.event != undefined">
                {{ errors.event }}
              </p>
              <select
                class="form-field"
                id="event"
                @change="changeEvent"
                v-model="webhook.event"
              >
                <option>customer_created</option>
                <option>payment_processed</option>
                <option>subscription_created</option>
                <option>subscription_cancelled</option>
                <option>trial_started</option>
                <option>trial_ended</option>
                <option>trial_converted</option>
                <option>tax_country_threshold_reached</option>
                <option>tax_state_threshold_reached</option>
                <option>workflow_failure</option>
              </select>
              <p class="form-field-help">
                {{
                  $t(
                    'app.system.integrations.slack.notifications.create.help_info.event'
                  )
                }}
              </p>
            </div>

            <div class="form-field-ctn mt-4">
              <label class="form-field-lbl" for="webhook">
                {{
                  $t(
                    'app.system.integrations.slack.notifications.create.fields.webhook'
                  )
                }}
              </label>
              <p class="form-field-error" v-if="errors.webhook != undefined">
                {{ errors.webhook }}
              </p>
              <select class="form-field" id="webhook" v-model="webhook.webhook">
                <option v-for="webhook in webhooks" :value="webhook.id">
                  {{ webhook.name }}
                </option>
              </select>
              <p class="form-field-help">
                {{
                  $t(
                    'app.system.integrations.slack.notifications.create.help_info.webhook'
                  )
                }}
              </p>
            </div>

            <div class="form-field-ctn mt-4">
              <label class="form-field-lbl" for="webhook">
                {{
                  $t(
                    'app.system.integrations.slack.notifications.create.fields.template'
                  )
                }}
              </label>
              <p class="form-field-error" v-if="errors.template != undefined">
                {{ errors.template }}
              </p>
              <textarea
                cols="90"
                rows="5"
                class="form-field"
                v-model="webhook.template"
              ></textarea>
              <p
                class="form-field-help"
                v-html="
                  $t(
                    'app.system.integrations.slack.notifications.create.help_info.template'
                  )
                "
              ></p>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Select, Textarea } from 'flowbite-vue'
import { useForm } from '../../../../composables/useForm'
import { useApi } from '../../../../composables/useApi'

// Router
const router = useRouter()

// API for loading webhooks
const { get } = useApi()

// Component state
const webhooks = ref([])

// Initial form data
const initialWebhookData = {
  event: '',
  webhook: '',
  template: '',
}

// Form handling with useForm composable
const {
  formData: webhook,
  isSubmitting: inProgress,
  errors,
  submitForm,
} = useForm(initialWebhookData)

// Load webhooks on mount
onMounted(async () => {
  try {
    const response = await get('/app/integrations/slack/notification/create')
    webhooks.value = response.data.webhooks
  } catch (error) {
    console.error('Failed to load webhooks:', error)
  }
})

// Event change handler with template population
const changeEvent = () => {
  const templates = {
    customer_created: 'A new customer ({{customer.email}}) has been created',
    payment_processed:
      'Successfully processed {{payment.amount_formatted}} for {{customer.email}}',
    payment_failure:
      'Failed to processed {{payment.amount_formatted}} for {{customer.email}}',
    subscription_created:
      'Successfully subscription {{subscription.plan_name}} for {{customer.email}}',
    subscription_cancelled:
      'Subscription {{subscription.plan_name}} for {{customer.email}} cancelled',
    trial_started:
      'Trial started for {{customer.email}} for {{subscription.plan_name}}',
    trial_ended:
      'Trial ended for {{customer.email}} for {{subscription.plan_name}}',
    trial_converted:
      'Trial converted into a subscription for {{customer.email}} for {{subscription.plan_name}}',
    tax_country_threshold_reached:
      'Tax country threshold of {{country.threshold}} reached for {{country.name}}',
    tax_state_threshold_reached:
      'Tax state threshold of {{state.threshold}} reached for {{ state.name }} in {{country.name}}',
    workflow_failure:
      'Workflow {{workflow}} failed to transition to {{transition}} because {{error_message}}',
  }

  webhook.template = templates[webhook.event] || ''
}

// Form submission
const save = async () => {
  try {
    await submitForm('/app/integrations/slack/notification/create', {
      onSuccess: () => {
        router.push({ name: 'app.system.integrations.slack.notification' })
      },
    })
  } catch (error) {
    // Error handling is managed by the useForm composable
  }
}
</script>

<style scoped></style>
