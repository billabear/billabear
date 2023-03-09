import axios from "axios";
import {handleResponse} from "./utils";

function invite(email) {
    return axios.post("/api/user/team/invite", {email})
        .then((response) => {
            const origResponse = response;
            if (response.name === 'AxiosError') {
                response = response.response;
            }

            if (response.status < 200 || response.status > 299) {
                const data = response.data;
                const error = (data && data.message) || data.error || data.errors || response.statusText;
                return Promise.reject(error);
            }

            if (response.data !== undefined && !response.data.success) {
                if (response.data.already_invited) {
                    return Promise.reject("User already invited");
                } else if (response.data.hit_limit) {
                    return Promise.reject("No more invites available");
                } else {
                    return Promise.reject("There was an unexpected error. Please try later.");
                }
            } else if (response.data === undefined || response.data === null) {
                return Promise.reject("There was an unexplained error");
            }

            return response;
    });
}

function getTeam() {
    return axios.get("/api/user/team")
        .then(handleResponse)
        .then((result) => {
            return {
                sent_invites: result.data.sent_invites,
                members: result.data.members,
            };
        });
}

function cancelInvite(invite) {
    return axios.post("/api/user/team/invite/"+invite.id+"/cancel").then(handleResponse);
}

function disableMember(member) {
    return axios.post("/api/user/team/member/"+member.id+"/disable").then(handleResponse);
}

export const teamservice = {
    invite,
    getTeam,
    cancelInvite,
    disableMember,
};