import axios from "axios";
import {handleResponse} from "./utils";

function login(username, password) {
    const payload = {
        username,
        password
    };

    return axios.post("/api/authenticate", payload).then(handleResponse)
}

function signup(user, code) {

    var url
    if (code === undefined) {
        url = `/api/user/signup`
    } else {
        url = '/api/user/signup/' + code
    }

    return axios.post(url, user).then(handleResponse);
}

function forgotPassword(email) {
    return axios.post("/api/user/reset", {email}).then(handleResponse);
}

function confirmEmail(code) {
    return axios.get(`/api/user/confirm/` + code, {
        headers: {'Content-Type': 'application/json'},
        data: {}
    }).then(handleResponse);
}

function forgotPasswordCheck(code) {
    return axios.get(`/api/user/reset/` + code, {
        headers: {'Content-Type': 'application/json'},
        data: {}
    }).then(handleResponse);
}
function changePassword(password, new_password)
{
    return axios.post(`/api/user/password`, {password, new_password}).then(handleResponse);
}

function forgotPasswordConfirm(code, password) {
    return axios.post(`/api/user/reset/` + code, {password}).then(handleResponse);
}

function fetchSettings() {
    return axios.get("/api/user/settings", {
        headers: {'Content-Type': 'application/json'},
        data: {}
    })
    .then(handleResponse)
    .then(result => {
        return result.data.form;
    });
}

function updateSettings(user) {
    return axios.post("/api/user/settings", user).then(handleResponse);
}

function invite(email) {
    return axios.post("/api/user/invite", {email}).then(handleResponse);
}
export const userservice = {
    login,
    signup,
    forgotPassword,
    forgotPasswordCheck,
    forgotPasswordConfirm,
    confirmEmail,
    fetchSettings,
    updateSettings,
    changePassword,
    invite,
};