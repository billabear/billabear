<template>
  <div class="text-center">
    <h3 class="mb-4 text-2xl font-semibold">{{ $t('portal.customer.manage.modal.cancel.title') }}</h3>
    <i class="fa-solid fa-circle-exclamation text-red-500 block text-5xl"></i>
    <p class="py-5">
      {{ $t('portal.customer.manage.modal.cancel.warning_message', {plan_name: subscription.plan.name}) }}
    </p>
    <SubmitButton :in-progress="sending" button-class="btn--danger" class="btn--danger" @click="sendCancel">{{ $t('portal.customer.manage.modal.cancel.button') }}</SubmitButton>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "CancelSubscription",
  props: {
    subscription: {
      type: Object,
    },
    token: {
      type: String,
    }
  },
  data() {
    return {
      sending: false,
      error: false,
    }
  },
  methods: {
    sendCancel: function () {
      this.sending = true;

      axios.post("/public/subscription/"+this.token+"/"+this.subscription.id+"/cancel").then(response => {
        this.$emit('close-modal');
      }).catch(error => {
        this.sending = false;
        this.error = true;
      })
    }
  }
}
</script>

<style scoped>

</style>