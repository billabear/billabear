<template>
  <div class="flex items-center justify-center h-screen login">
    <transition
        appear-active-class="duration-1000 ease-out"
        apear-to-class="opacity-300"

        enter-active-class="duration-500 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="transform opacity-300"

        leave-active-class="duration-500 ease-in"
        leave-from-class="opacity-300"
        leave-to-class="transform opacity-0"

        mode="out-in"

        appear>
      <div v-if="has_error">
        <div class="p-5 public-form-body animate-shake">
          <div class="px-5 text-center">
            {{ $t('public.confirm_email.error_message') }}
          </div>
        </div>
      </div>
      <div v-else-if="confirmed">
        <div class="p-5 public-form-body">
          <div class="px-5 text-center">
            {{ $t('public.confirm_email.success_message') }}
          </div>
          <div class="px-5 pt-2 text-center">
            <router-link :to="{name: 'public.login'}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $t('public.confirm_email.login_link') }}</router-link>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import {userservice} from "../../services/userservice";

export default {
  name: "ConfirmEmail",
  data() {
    return {
      has_error: false,
      confirmed: false,
    }
  },
  mounted() {
    this.code = this.$route.params.code
    userservice.confirmEmail(this.code).then(
        form => {
          this.confirmed = true;
        },
        error => {
          this.has_error = true;
        }
    )
  },
}
</script>

<style scoped>

</style>