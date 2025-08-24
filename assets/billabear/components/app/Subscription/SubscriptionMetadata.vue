<template>
  <div class="card-body">
    <div>
      <h2 class="section-header">{{ $t('app.subscription.view.metadata.title') }}</h2>
      <dl class="detail-list section-body">
        <div v-for="(value, key) in subscription.metadata" :key="key">
          <dt>{{ key }}</dt>
          <dd>{{ value }}</dd>
        </div>
      </dl>
      <div v-if="subscription.metadata.length === 0" class="w-full text-center italic text-gray-500">
        {{ $t('app.subscription.view.metadata.no_metadata') }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SubscriptionMetadata",
  props: {
    subscription: {
      type: Object,
      required: true
    }
  }
}
</script>