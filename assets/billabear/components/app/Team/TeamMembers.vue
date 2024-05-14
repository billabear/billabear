<template>
  <div class="card-body">
    <table class="w-full">
      <thead>
        <tr>
          <th>{{ $t('app.team.members.email') }}</th>
          <th>{{ $t('app.team.members.created_at') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="members.length === 0">
          <td colspan="3" class="text-center">{{ $t('app.team.members.no_members') }}</td>
        </tr>
        <tr v-for="member in members">
          <td>{{ member.email }}</td>
          <td>{{ member.created_at }}</td>
          <td>
            <span class="badge--green" v-if="!member.is_deleted">{{ $t('app.team.members.active') }}</span>
            <span class="badge--red" v-else>{{ $t('app.team.members.disabled') }}</span>
          </td>
          <td v-if="member.id !== user.id && !member.is_deleted" class="text-end">
            <button v-if="(current_member !== undefined && current_member.id !== member.id) || !disable_member_in_progress" class="btn--danger" :class="{'btn--danger--disabled': disable_member_in_progress}" :disabled="disable_member_in_progress" @click="disableMember({member})">
              {{ $t('app.team.members.disable') }}
            </button>
            <button class="btn--danger--disabled" disabled v-else>
              <LoadingMessage>{{ $t('app.team.members.disabling') }}</LoadingMessage>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';

export default {
  name: "TeamMembers",
  computed: {
    ...mapState('userStore', ['user']),
    ...mapState('teamStore', ['members', 'current_member', 'disable_member_in_progress'])
  },
  methods: {
    ...mapActions('teamStore', ['disableMember'])
  }
}
</script>

<style scoped>

</style>