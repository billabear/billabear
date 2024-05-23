<template>
  <div>
    <h1 class="page-title">{{ $t('app.economic_area.member.edit.title') }}</h1>

    <LoadingScreen :ready="ready">
      <div class="mx-5 card-body">

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.economic_area.member.edit.fields.country') }}
          </label>
          <p class="form-field-error" v-if="errors.country != undefined">{{ errors.country }}</p>
          <p>{{ member.country.name }}</p>
          <p class="form-field-help">{{ $t('app.economic_area.member.edit.help_info.country') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.economic_area.member.edit.fields.joined_at') }}
          </label>
          <p class="form-field-error" v-if="errors.joinedAt != undefined">{{ errors.joinedAt }}</p>
          <VueDatePicker class="mt-2" v-model="member.joined_at" :enable-time-picker="false"></VueDatePicker>
          <p class="form-field-help">{{ $t('app.economic_area.member.edit.help_info.joined_at') }}</p>
        </div>

        <div class="form-field-ctn">
          <label class="form-field-lbl" for="name">
            {{ $t('app.economic_area.member.edit.fields.left_at') }}
          </label>
          <p class="form-field-error" v-if="errors.leftAt != undefined">{{ errors.leftAt }}</p>
          <VueDatePicker class="mt-2" v-model="member.left_at" :enable-time-picker="false"></VueDatePicker>
          <p class="form-field-help">{{ $t('app.economic_area.member.edit.help_info.left_at') }}</p>
        </div>
      </div>
      <div class="m-5">
        <SubmitButton :in-progress="sending" @click="send">{{ $t('app.economic_area.member.edit.edit_button') }}</SubmitButton>
      </div>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
export default {
  name: "EconomicAreaMemberEdit",
  data() {
    return {
      member: {},
      countries: [],
      ready: false,
      sending: false,
      errors: {}
    }
  },
  mounted() {
    const memberId = this.$route.params.memberId
    axios.get("/app/economic-area/member/"+memberId+"/view").then(response => {
      this.member = response.data;
      this.ready = true;
    })
  },
  methods: {
    send: function () {
      const id = this.$route.params.id
      const memberId = this.$route.params.memberId
      this.member.economic_area = id;

      this.member.joined_at = new Date(Date.parse(this.member.joined_at)).toISOString()

      if (this.member.left_at) {
        this.member.left_at = new Date(Date.parse(this.member.left_at)).toISOString()
      }

      this.errors = {};
      this.sending = true;

      axios.post("/app/economic-area/member/"+memberId+"/update", this.member).then(response => {
        this.$router.push({name: 'app.finance.economic_area.view', params: {id: id}})
      }).catch(error => {
        this.errors = error.response.data.errors;
        this.sending = false;
      })
    }
  }
}
</script>

<style scoped>

</style>
