<template>
  <div>
    <h1 class="page-title">{{ $t('app.settings.user.update.title') }}</h1>

    <LoadingScreen :ready="ready">
      <form @submit.prevent="save">
        <div class="mt-3 card-body">
          <div class="form-field-ctn">
            <label class="form-field-lbl" for="email">
              {{ $t('app.settings.user.update.fields.email') }}
            </label>
            <p class="form-field-error" v-if="errors.email != undefined">{{ errors.email }}</p>
            <input type="email" class="form-field" id="email" v-model="user.email"  />
            <p class="form-field-help">{{ $t('app.settings.user.update.help_info.email') }}</p>
          </div>

          <div class="form-field-ctn">
            <label class="form-field-lbl" for="roles">
              {{ $t('app.settings.user.update.fields.roles') }}
            </label>
            <p class="form-field-error" v-if="errors.roles != undefined">{{ errors.roles }}</p>
            <div class="grid grid-cols-2">
              <div v-for="role in roles">
                <input type="checkbox"  :id="'checkbox_'+role" :value="role" v-model="user.roles"/>
                <label :for="'checkbox_'+role">{{ role }}</label>
              </div>
            </div>
            <p class="form-field-help">{{ $t('app.settings.user.update.help_info.roles') }}</p>
          </div>


        </div>

        <div class="form-field-submit-ctn">
          <SubmitButton :in-progress="sending">{{ $t('app.settings.user.update.submit_btn') }}</SubmitButton>
        </div>
        <p class="text-green-500 font-weight-bold" v-if="success">{{ $t('app.settings.user.update.success_message') }}</p>
      </form>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "SettingsUserUpdate",
  data() {
    return {
      sending: false,
      ready: false,
      success: false,
      user: {},
      errors: {},
      roles: [],
    }
  },
  mounted() {
    const id = this.$route.params.id;
    axios.get('/app/settings/user/'+id).then(response => {
      this.user = response.data.user;
      this.roles = response.data.roles;
      this.ready = true;
    })
  },
  methods: {
    save: function () {
      this.sending = true;
      this.errors = {};
      const id = this.$route.params.id;
      axios.post('/app/settings/user/'+id, this.user).then(response => {
        this.sending = false;
        this.success = true;
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.sending = false;
        this.success = false;
      })
    }
  }
}
</script>

<style scoped>

</style>