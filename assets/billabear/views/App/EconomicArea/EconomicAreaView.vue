<template>
  <div>
    <h1 class="page-title">{{ $t('app.economic_area.view.title') }}</h1>
    <LoadingScreen :ready="ready">
      <div class="m-5 card-body">

        <div class="section-body">
          <dl class="detail-list">
            <div>
              <dt>{{ $t('app.economic_area.view.fields.name') }}</dt>
              <dd>{{ area.name }}</dd>
            </div>
            <div>
              <dt>{{ $t('app.economic_area.view.fields.threshold') }}</dt>
              <dd><Currency :amount="area.threshold" /></dd>
            </div>
            <div>
              <dt>{{ $t('app.economic_area.view.fields.currency') }}</dt>
              <dd>{{ area.currency }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <div class="mt-5">
        <div class="mx-5 grid grid-cols-2">
          <div class="text-2xl">{{ $t('app.economic_area.view.members.title') }}</div>
          <div class="text-end"><router-link class="btn--main" :to="{name: 'app.finance.economic_area.member.create', params: {id:area.id}}">{{ $t('app.economic_area.view.members.create_new') }}</router-link></div>
        </div>


        <table class="list-table">
          <thead>
          <tr>
            <th>{{ $t('app.economic_area.view.members.list.name') }}</th>
            <th>{{ $t('app.economic_area.view.members.list.joined_at')}}</th>
            <th>{{ $t('app.economic_area.view.members.list.left_at')}}</th>
            <th></th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="member in area.members">
              <td>{{ member.country.name }}</td>
              <td>{{ member.joined_at }}</td>
              <td>{{ member.left_at }}</td>
              <td>
                <button class="btn--danger mr-2" @click="startDelete(member.id)">
                  <i class="fa-solid fa-trash"></i>
                </button>
                <router-link :to="{name: 'app.finance.economic_area.member.edit', params: {id:area.id, memberId: member.id}}" class="btn--main">{{ $t('app.economic_area.view.members.list.edit_button')}}</router-link>
              </td>
            </tr>
            <tr v-if="area.members.length === 0">
              <td colspan="4" class="text-center">{{ $t('app.economic_area.view.members.list.no_members')}}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <VueFinalModal
          :model-value="showDelete"
          class="flex justify-center items-center"
          content-class="max-w-xl mx-4 p-4 bg-white dark:bg-gray-900 border dark:border-gray-700 rounded-lg space-y-2">
        {{ $t('app.economic_area.view.delete_modal.text') }}
        <div class="mt-2 text-center">
          <button class="btn--main mr-2" @click="showDelete = false">{{ $t('app.economic_area.view.delete_modal.cancel') }}</button>
          <button class="btn--danger" @click="completeDelete">{{ $t('app.economic_area.view.delete_modal.delete') }}</button>
        </div>
      </VueFinalModal>
    </LoadingScreen>
  </div>
</template>

<script>
import axios from "axios";
import Currency from "../../../components/app/Currency.vue";
import {VueFinalModal} from "vue-final-modal";
import {Button} from "flowbite-vue";

export default {
  name: "EconomicAreaView",
  components: {Button, VueFinalModal, Currency},
  data() {
    return {
      showDelete: false,
      ready: false,
      area: {},
      activeMemberId: null,
    }
  },
  mounted() {
    const id = this.$route.params.id
    axios.get("/app/economic-area/"+id+"/view").then((response) => {
      this.ready = true;
      this.area = response.data;
    })
  },
  methods: {
    startDelete: function (id  ) {
      this.activeMemberId = id;
      this.showDelete = true
    },
    completeDelete: function() {
      const id = this.$route.params.id
      for (var i = 0; i < this.area.members.length; i++) {
        var member = this.area.members[i];
        if (member.id === this.activeMemberId) {
          axios.post("/app/economic-area/member/"+this.activeMemberId+"/delete").then(response => {
            this.area.members.splice(i, 1);
            this.showDelete = false;
          })
          return;
        }
      }
    }
  }
}
</script>

<style scoped>

</style>
