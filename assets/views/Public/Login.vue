<template>
  <div class="flex items-center justify-center h-screen login">
    <form @submit.prevent="handleSubmit">
      <div class="p-5 public-form-body" :class="{'animate-shake': error_info.has_error}">
        <div class="w-full">
          <PublicLogo />
        </div>
        <h1 class="h1 text-center">{{ $t('public.login.title') }}</h1>
        <div class="px-5 mt-2 mb-3" v-if="error_info.has_error">
          <div class="alert-error text-center">{{ error_info.message }}</div>
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('public.login.email') }}</label>
          <input type="text" class="input-field" v-model="email" />
        </div>
        <div class="px-5 mb-3">
          <label class="block mb-1">{{ $t('public.login.password') }}</label>
          <input type="password" class="input-field" v-model="password" />
        </div>
        <div class="px-5 mb-3 flex items-center justify-between">
          <router-link :to="{name: 'public.forgot_password'}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $t('public.login.forgot_password_link') }}</router-link>
        </div>
        <div class="px-5">
          <button type="submit" class="btn--main w-full" v-if="!in_progress">{{ $t('public.login.login_button') }}</button>
          <button type="submit" class="btn--main--disabled w-full cursor-not-allowed" v-else>
            <LoadingMessage>{{ $t('public.login.logging_in') }}</LoadingMessage>
          </button>
        </div>
        <div class="mt-5 px-5 mb-3  text-center">
          <router-link :to="{name: 'public.signup'}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $t('public.login.signup_link') }}</router-link>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import PublicLogo from "../../components/public/PublicLogo";

export default {
  name: "Login",
  components: {PublicLogo},
  data () {
    return {
      email: '',
      password: ''
    }
  },
  computed: {
    ...mapState('userStore', ['status', 'error_info', 'in_progress'])
  },
  methods: {
    ...mapActions('userStore', ['login', 'logout']),
    handleSubmit (e) {
      const username = this.email;
      const password = this.password;
      this.login( {username, password} )
    }
  }
}
</script>

<style scoped>

</style>