<template>
  <LoadingScreen :ready="loading" :loading-message="$t('global.loading')">
      <h1 class="page-title">{{ $t('app.user.settings.title') }}</h1>

      <div v-if="alert !== undefined" class="mt-5" :class="{'alert-error': alert.type==='error','alert-success': alert.type==='success'}">
        {{ alert.message }}
      </div>

      <form @submit.prevent="save">

        <div class="mt-5 card-body">
          <label class="label">{{ $t('app.user.settings.name') }}</label>
          <input type="text" class="form-field" :class="{'form-error': errors.name !== undefined}" v-model="user.name" />
          <span class="error-message" v-if="errors.name" v-for="error in errors.name">{{ error }}</span>

          <label class="label">{{ $t('app.user.settings.email') }}</label>
          <input type="text" class="form-field"  :class="{'form-error': errors.email !== undefined}"  v-model="user.email" />
          <span class="error-message" v-if="errors.email" v-for="error in errors.email">{{ error }}</span>
        </div>

        <div class="mt-3">
          <SubmitButton :in-progress="sending_settings" :loading-text="$t('app.user.settings.in_progress')">{{ $t('app.user.settings.save') }}</SubmitButton>
        </div>
      </form>

      <h2 class="h2 text-slate-500 mt-3 cursor-pointer" @click="show_danger_zone = !show_danger_zone">{{ $t('app.user.settings.danger_zone') }} <i class="fa-solid fa-caret-down" v-if="!show_danger_zone"></i><i class="fa-solid fa-caret-up" v-else></i></h2>

      <form @submit.prevent="changePassword" v-if="show_danger_zone">
        <div class="card-body" >

          <label class="label">{{ $t('app.user.settings.current_password') }}</label>
          <input type="password" class="form-field" v-model="current_password" />
          <span class="error-message" v-if="need_current_password">{{ $t('app.user.settings.need_current_password') }}</span>

          <label class="label">{{ $t('app.user.settings.new_password') }}</label>
          <input type="password" name="password" class="form-field" v-model="new_password" />
          <span class="error-message" v-if="need_new_password">{{ $t('app.user.settings.need_new_password') }}</span>
          <span class="error-message" v-if="need_valid_password">{{ $t('app.user.settings.need_valid_password') }}</span>

          <label class="label">{{ $t('app.user.settings.new_password_again') }}</label>
          <input type="password" class="form-field" v-model="new_password_again" />
          <span class="error-message" v-if="need_passwords_to_match">{{ $t('app.user.settings.need_password_to_match') }}</span>
        </div>
        <div class="mt-3">
          <SubmitButton :in-progress="sending_password" :loading-text="$t('app.user.settings.in_progress')">
            {{ $t('app.user.settings.change_password') }}
          </SubmitButton>
        </div>
      </form>
  </LoadingScreen>
</template>

<script>
import {userservice} from "../../../services/userservice";

export default {
  name: "UserSettings",
  data() {
    return {
      loading: false,
      sending_settings: false,
      sending_password: false,
      need_current_password: false,
      need_new_password: false,
      need_passwords_to_match: false,
      need_valid_password: false,
      user: {},
      error_message: undefined,
      alert: undefined,
      errors: {},
      current_password: "",
      new_password: "",
      new_password_again: "",
      show_danger_zone: false,
    }
  },
  mounted() {
    userservice.fetchSettings().then(
        user => {
          this.user = user;
          this.loading = true;
        }
    )
  },
  methods: {
    changePassword: function () {
        var hasErrors = false;
        this.need_current_password = false;
        this.need_new_password = false;
        this.need_valid_password = false;
        this.need_passwords_to_match = false;

        if (this.current_password === "") {
          this.need_current_password = true;
          hasErrors = true;
        }

        if (this.new_password === "") {
          this.need_new_password = true;
          hasErrors = true;
        } else if (this.new_password.length < 8) {
          this.need_valid_password = true;
          hasErrors = true;
        } else if (this.new_password !== this.new_password_again) {
          this.need_passwords_to_match = true;
          hasErrors = true;
        }

        if (hasErrors) {
          return;
        }
        this.sending_password = true;
        userservice.changePassword(this.current_password, this.new_password).then(
            result => {
              this.alert = {
                type: "success",
                message: this.$t("app.user.settings.success_message")
              }
              this.sending_password = false;
            },
            error => {
              this.alert = {
                type: "error",
                message: error,
              }
              this.sending_password = false;
            }
        )
    },
    save: function () {
      this.sending_settings = true;
      return;
      userservice.updateSettings(this.user).then(
          user => {
            this.alert = {
              type: "success",
              message: this.$t("app.user.settings.success_message")
            };
            this.sending_settings = false;
          },
          errors => {
            this.errors = errors
            this.alert = {
              type: "error",
              message: this.$t("app.user.settings.error_message")
            };
            this.sending_settings = false;
          }
      )
    }
  }
}
</script>

<style scoped>
.spinner {
  @apply animate-spin -ml-1 mr-3 h-5 w-5 text-white inline;
}
</style>