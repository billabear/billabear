<template>
  <div>
    <h3 class="h3 my-5">{{ $t('app.team.pending_invites.title') }}</h3>
    <div class="card-body">
      <table class="w-full">
        <thead>
          <tr>
            <th>{{ $t('app.team.pending_invites.email') }}</th>
            <th>{{ $t('app.team.pending_invites.invited_at') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="sent_invites.length === 0">
            <td colspan="3" class="text-center">{{ $t('app.team.pending_invites.none') }}</td>
          </tr>
          <tr v-for="invite in sent_invites">
            <td>{{ invite.email }}</td>
            <td>{{ invite.created_at }}</td>
            <td class="text-end">
              <button class="btn--danger" @click="cancelInvite({invite})" :disabled="cancel_invite_in_progress" :class="{'btn--danger--disabled': cancel_invite_in_progress}" v-if="(current_invite != undefined && current_invite.id !== invite.id) || !cancel_invite_in_progress">{{ $t('app.team.pending_invites.cancel') }}</button>
              <button type="submit" class="btn--danger--disabled" v-else>
                <LoadingMessage>{{ $t('app.team.pending_invites.cancelling') }}</LoadingMessage>

              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
  name: "TeamPendingInvites",
  computed: {
    ...mapState('teamStore', ['sent_invites', 'current_invite' , 'cancel_invite_in_progress'])
  },
  methods: {
    ...mapActions('teamStore', ['cancelInvite'])
  }
}
</script>

<style scoped>

</style>