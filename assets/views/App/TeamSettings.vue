<template>
  <div>
    <h1 class="page-title">{{ $t('app.team.main.title') }}</h1>

    <div class="top-button-container">
      <button class="btn--main" :class="{'btn--main--disabled': show_invite_form}"  @click="showInviteForm" :disabled="show_invite_form"><i class="fa-solid fa-user-plus"></i> {{ $t('app.team.main.add_team_member') }}</button>
    </div>

    <TeamInvite v-if="show_invite_form" />

    <TeamMembers />

    <TeamPendingInvites />
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import TeamInvite from "../../components/app/Team/TeamInvite";
import TeamPendingInvites from "../../components/app/Team/TeamPendingInvites";
import TeamMembers from "../../components/app/Team/TeamMembers";

export default {
  name: "TeamSettings",
  components: {TeamMembers, TeamPendingInvites, TeamInvite},
  computed: {
    ...mapState('teamStore', ['show_invite_form', 'sent_invites', 'members'])
  },
  mounted() {
    this.loadTeamInfo()
  },
  methods: {
    ...mapActions('teamStore', ['showInviteForm', 'loadTeamInfo']),
    displayInviteForm: function () {
      this.showInviteForm()
    }
  }
}
</script>

<style scoped>

</style>