<template>
  <div class="flex items-center justify-center h-screen login">
    <div v-if="loaded">
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
        <form @submit.prevent="handleSubmit" v-if="!successfully_progress">
          <div class="p-5 public-form-body" :class="{'animate-shake': error_info.has_error}">
            <div class="w-full">
              <PublicLogo />
            </div>
            <h1 class="h1 text-center">{{ $t('public.forgot_password_confirm.title') }}</h1>
            <div class="px-5 mt-2 mb-3" v-if="error_info.has_error">
              <div class="alert-error text-center">{{ error_info.message }}</div>
            </div>
            <div class="px-5 mb-3">
              <label class="block mb-1">{{ $t('public.forgot_password_confirm.password') }}</label>
              <input type="password" class="input-field" v-model="password" />
              <span class="block text-red-500" v-if="password_error !== undefined">{{ password_error }}</span>
            </div>
            <div class="px-5 mb-3">
              <label class="block mb-1">{{ $t('public.forgot_password_confirm.password_confirm') }}</label>
              <input type="password" class="input-field" v-model="password_confirm" />
              <span class="block text-red-500" v-if="password_confirm_error !== undefined">{{ password_confirm_error }}</span>
            </div>
            <div class="px-5">
              <button type="submit" class="btn--main w-full" v-if="!in_progress">{{ $t('public.forgot_password_confirm.reset_button') }}</button>
              <button type="submit" class="btn--main--disabled w-full cursor-not-allowed" v-else>
                <LoadingMessage>{{ $t('public.forgot_password_confirm.in_progress') }}</LoadingMessage>
              </button>
            </div>
          </div>
        </form>
        <div v-else>
          <div class="p-5 public-form-body">
            <div class="px-5">
              {{ $t('public.forgot_password_confirm.success_message') }}

            </div>
            <div class="px-5 pt-2 text-center">
              <router-link :to="{name: 'public.login'}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $t('public.forgot_password_confirm.login_link') }}</router-link>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import {userservice} from "../../services/userservice";
import PublicLogo from "../../components/public/PublicLogo";

export default {
  name: "ForgotPasswordConfirm",
  components: {PublicLogo},
  data() {
    return {
      code: "",
      in_progress: false,
      password: "",
      password_confirm: "",
      password_error: undefined,
      loaded: false,
      successfully_progress: false,
      error_info: {
        has_error: false,
        message: undefined
      }
    }
  },
  mounted() {
    this.code = this.$route.params.code
    userservice.forgotPasswordCheck(this.code).then(
        form => {
          this.loaded = true
        },
        error => {
          this.$router.push("/login")
        }
    )
  },
  methods: {
    handleSubmit: function () {
      this.submitted = true;
      this.password_error = undefined;
      this.password_confirm_error = undefined;
      this.error_info = {has_error: false, message: undefined};
      var hasError = false;
      var hasPassword = false;

      if (this.password === "") {
        this.password_error = this.$t('public.forgot_password_confirm.password_error');
        hasError = true;
      } else if (this.password.length < 8) {
        this.password_error = this.$t('public.forgot_password_confirm.password_length_error');
        hasError = true;
      } else {
        hasPassword = true;
      }

      if (this.password !== this.password_confirm) {
        this.password_confirm_error = this.$t('public.forgot_password_confirm.password_confirm_error');
        hasError = true;
      }

      if (hasError) {
        return;
      }
      this.in_progress=true;
      userservice.forgotPasswordConfirm(this.code, this.password).then(
          response => {
            this.successfully_progress = true;
          },
          error => {
            this.in_progress = false;
            this.error_info = {
              has_error: true,
              message: error
            }
          }
      );
    }
  }
}
</script>

<style scoped>

</style>