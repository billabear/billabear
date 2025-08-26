<template>
  <div class="w-screen">
    <div
      class="mx-auto login max-w-2xl mt-12 mb-12 border rounded-lg bg-white shadow p-5"
    >
      <form @submit.prevent="handleSubmit">
        <div
          class="p-5 public-form-body"
          :class="{ 'animate-shake': error_info.has_error }"
        >
          <div class="w-full">
            <PublicLogo />
          </div>
          <h1 class="h1 text-center text-3xl">
            {{ $t('public.login.title') }}
          </h1>
          <div class="px-5 mt-2 mb-3" v-if="error_info.has_error">
            <div class="alert-error text-center">{{ error_info.message }}</div>
          </div>
          <div class="px-5 mb-3">
            <label class="block mb-1">{{ $t('public.login.email') }}</label>
            <input type="text" class="form-field w-full" v-model="email" />
          </div>
          <div class="px-5 mb-3">
            <label class="block mb-1">{{ $t('public.login.password') }}</label>
            <input
              type="password"
              class="form-field w-full"
              v-model="password"
            />
          </div>
          <div class="px-5 mb-3 flex items-center justify-between">
            <router-link
              :to="{ name: 'public.forgot_password' }"
              class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500"
              >{{ $t('public.login.forgot_password_link') }}</router-link
            >
          </div>
          <div class="px-5">
            <button type="submit" class="btn--main w-full" v-if="!in_progress">
              {{ $t('public.login.login_button') }}
            </button>
            <button
              type="submit"
              class="btn--main--disabled w-full cursor-not-allowed"
              v-else
            >
              <LoadingMessage>{{
                $t('public.login.logging_in')
              }}</LoadingMessage>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'
import PublicLogo from '../../components/public/PublicLogo.vue'

export default {
  name: 'Login',
  components: { PublicLogo },
  data() {
    return {
      email: '',
      password: '',
    }
  },
  computed: {
    ...mapState('userStore', ['status', 'error_info', 'in_progress']),
  },
  methods: {
    ...mapActions('userStore', ['login', 'logout']),
    handleSubmit(e) {
      const username = this.email
      const password = this.password
      this.login({ username, password })
    },
  },
}
</script>

<style scoped></style>
