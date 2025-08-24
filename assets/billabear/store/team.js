import { defineStore } from 'pinia';
import { teamservice } from "../services/teamservice";

export const useTeamStore = defineStore('team', {
    state: () => ({
        show_invite_form: false,
        invite_sending_in_progress: false,
        invite_successfully_processed: false,
        invite_error: undefined,
        current_invite: undefined,
        cancel_invite_in_progress: false,
        sent_invites: [],
        members: [],
        team_error: undefined,
        disable_member_in_progress: false,
        current_member: undefined,
    }),

    actions: {
        async cancelInvite({ invite }) {
            this.startCancelInvite(invite);
            try {
                await teamservice.cancelInvite(invite);
                this.removeInvite(invite);
            } catch (error) {
                this.setTeamError(error);
            }
        },

        async disableMember({ member }) {
            this.startDisableMember(member);
            try {
                await teamservice.disableMember(member);
                this.markMemberAsDisabled(member);
            } catch (error) {
                this.setTeamError(error);
            }
        },

        showInviteForm() {
            this.showInviteFormEnable();
        },

        hideInviteForm() {
            this.showInviteFormDisable();
        },

        sendAnother() {
            this.resetInvite();
        },

        async sendInvite({ email }) {
            this.startInviteProcess();

            if (email === "" || email === undefined || email === null) {
                this.inviteError('An email must be provided');
                return;
            }

            try {
                await teamservice.invite(email);
                this.inviteSent();
            } catch (error) {
                this.inviteError(error);
            }
        },

        async loadTeamInfo() {
            try {
                const result = await teamservice.getTeam();
                this.setTeamInfo(result);
            } catch (error) {
                this.setTeamError(error);
            }
        },

        // Mutation-like methods (now just regular methods in Pinia)
        startDisableMember(member) {
            this.disable_member_in_progress = true;
            this.current_member = member;
        },

        markMemberAsDisabled(member) {
            this.disable_member_in_progress = false;
            const index = this.members.indexOf(member);
            this.members[index].is_deleted = true;
        },

        startCancelInvite(invite) {
            this.current_invite = invite;
            this.cancel_invite_in_progress = true;
        },

        removeInvite(invite) {
            const invites = this.sent_invites;
            const index = invites.indexOf(invite);
            invites.splice(index, 1);

            this.current_invite = undefined;
            this.cancel_invite_in_progress = false;
        },

        setTeamInfo(result) {
            this.sent_invites = result.sent_invites;
            this.members = result.members;
        },

        setTeamError(error) {
            this.team_error = error;
            this.cancel_invite_in_progress = false;
            this.invite_sending_in_progress = false;
        },

        resetInvite() {
            this.invite_successfully_processed = false;
            this.invite_error = undefined;
        },

        showInviteFormEnable() {
            this.show_invite_form = true;
        },

        showInviteFormDisable() {
            this.show_invite_form = false;
        },

        startInviteProcess() {
            this.invite_sending_in_progress = true;
            this.invite_error = undefined;
        },

        inviteError(error) {
            this.invite_sending_in_progress = false;
            this.invite_error = error;
        },

        inviteSent() {
            this.invite_error = undefined;
            this.invite_sending_in_progress = false;
            this.invite_successfully_processed = true;
        }
    }
});
