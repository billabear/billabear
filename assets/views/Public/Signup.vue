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
      <form @submit.prevent="handleSubmit" v-if="signing_up">
        <div class="p-5 public-form-body" :class="{'animate-shake': error_info.has_error}">
          <div class="w-full">
            <PublicLogo />
          </div>
          <h1 class="h1 text-center">{{ $t('public.signup.title') }}</h1>
          <div class="px-5 mb-3">
            <label class="block mb-1">{{ $t('public.signup.email') }}</label>
            <input type="text" class="input-field" v-model="email" />
            <span class="block text-red-500" v-if="email_error !== undefined">{{ email_error }}</span>
          </div>
          <div class="px-5 mb-3">
            <label class="block mb-1">{{ $t('public.signup.password') }}</label>
            <input type="password" class="input-field" v-model="password" />
            <span class="block text-red-500" v-if="password_error !== undefined">{{ password_error }}</span>
          </div>
          <div class="px-5 mb-3">
            <label class="block mb-1">{{ $t('public.signup.password_confirm') }}</label>
            <input type="password" class="input-field" v-model="password_confirm" />
            <span class="block text-red-500" v-if="password_confirm_error !== undefined">{{ password_confirm_error }}</span>
          </div>
          <div class="px-5 pt-3">
            <SubmitButton class="w-full" :in-progress="in_process" :loading-text="$t('public.signup.signing_up')">
              {{ $t('public.signup.signup_button') }}
            </SubmitButton>
          </div>
          <div class="mt-5 px-5 mb-3  text-center">
            <router-link :to="{name: 'public.login'}" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">{{ $t('public.signup.login_link') }}</router-link>
          </div>
        </div>
      </form>
      <div v-else>
        <div class="p-5 public-form-body">
          <div class="px-5">
            {{ $t('public.signup.success_message') }}
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import {userservice} from "../../services/userservice";
import PublicLogo from "../../components/public/PublicLogo";
import {mapActions} from "vuex";

export default {
  name: "Signup",
  components: {PublicLogo},
  data() {
    return {
      email: '',
      email_error: undefined,
      password: '',
      password_error: undefined,
      password_confirm: '',
      password_confirm_error: undefined,
      error: '',
      signing_up: true,
      in_process: false,
      error_info: {
        has_error: false,
      }
    }
  },
  methods: {
    ...mapActions('userStore', ['markAsLoggedin']),
    handleSubmit (e) {
      this.submitted = true;
      const { email, password, password_confirm } = this;
      var user = {email, password}
      var hasError = false

      this.email_error = undefined
      this.password_error = undefined
      this.password_confirm_error = undefined

      if (email === "") {
        this.email_error = this.$t('public.signup.email_error')
        hasError = true;
      }

      if (password === "") {
        this.password_error = this.$t('public.signup.password_error')
        hasError = true;
      } else if (password !== password_confirm) {
        this.password_confirm_error = this.$t('public.signup.password_confirm_error')
        hasError = true;
      }

      if (hasError) {
        this.error_info = {has_error: true};
        return;
      }

      this.in_process = true
      var that = this
      if (email && password) {
        userservice.signup(user, this.$route.params.code)
            .then(
                response => {

                  this.signing_up = false
                  if (response.data.user !== undefined) {
                    that.$router.push({name: 'app.home'})
                  }
                  var user = response.data.user
                  this.markAsLoggedin({user});
                },
                error => {
                  this.error = error
                }
            );
      }
    }
  }
}
</script>

<style scoped>

.animate-fade-in-second {
  animation: fadeIn 1s;
}

.animate-fade-out {
  animation: fadeOut 1s;
}
</style>