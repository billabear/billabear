<template>
  <LoadingScreen :ready="false">
    <div class="text-center"><ErrorBear /></div>
  </LoadingScreen>
</template>

<script>
import axios from "axios";
import {mapActions} from "vuex";
import {router} from "../../helpers/router";
import ErrorBear from "../../components/app/ErrorBear.vue";

export default {
  name: "LoginLink",
  components: {ErrorBear},
  data() {
    return {
      ready: false,
    }
  },
  methods: {
    ...mapActions('userStore', ['markAsLoggedin']),
  },
  mounted() {
    axios.get("/app/login_check" + window.location.search).then(response => {
      const user = response.data;
      this.markAsLoggedin({user});
      router.push('/site/home');
    }).catch(error => {
        this.ready = true;
    })
  }
}
</script>

<style scoped>

</style>
