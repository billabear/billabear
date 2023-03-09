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
      <form @submit.prevent="handleSubmit" v-if="!password_requested">
        <div class="p-5 public-form-body" :class="{'animate-shake': error_info.has_error}">
          <div class="w-full">
            <PublicLogo />
          </div>
          <h1 class="h1 text-center">{{ $t('public.forgot_password.title') }}</h1>
          <div class="px-5 mt-2 mb-3" v-if="error_info.has_error">
            <div class="alert-error text-center">{{ error_info.message }}</div>
          </div>
          <div class="px-5 mb-3">
            <label class="block mb-1">{{ $t('public.forgot_password.email') }}</label>
            <input type="text" class="input-field" v-model="email" />
            <span class="block text-red-500" v-if="email_error !== undefined">{{ email_error }}</span>
          </div>
          <div class="px-5">
            <button type="submit" class="btn--main w-full" v-if="!in_progress">{{ $t('public.forgot_password.request_button') }}</button>
            <button type="submit" class="btn--main--disabled w-full cursor-not-allowed" v-else>
              <LoadingMessage>{{ $t('public.forgot_password.in_progress') }}</LoadingMessage>
            </button>
          </div>
          <div class="mt-5 px-5 mb-3  text-center">
            <router-link :to="{name: 'public.login'}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $t('public.forgot_password.login_link') }}</router-link>
          </div>
        </div>
      </form>
      <div v-else>
        <div class="p-5 public-form-body">
          <div class="px-5">
            {{ $t('public.forgot_password.success_message') }}
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import {userservice} from "../../services/userservice";
import PublicLogo from "../../components/public/PublicLogo";

export default {
  name: "ForgotPassword",
  components: {PublicLogo},
  data() {
    return {
      error_info: {
        has_error: false,
        message: undefined,
      },
      email: "",
      email_error: undefined,
      in_progress: false,
      password_requested: false,
    }
  },
  methods: {
    handleSubmit: function () {
      const email = this.email;
      var hasError = false;
      if (email === "") {
        this.email_error = this.$t('public.forgot_password.email_error');
        hasError = true;
      }

      if (hasError) {
        return;
      }
      this.in_progress = true

      userservice.forgotPassword(email).then(
          user => {
            this.in_progress = false
            this.password_requested=true
          },
          error => {
            this.error_info = {
              has_error: true,
              message: error
            };
            this.in_progress = true;
          })

    }
  }
}
</script>

<style scoped>

</style>