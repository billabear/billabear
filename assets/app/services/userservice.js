import axios from "axios";
import {handleResponse} from "./utils";

function login(username, password) {
    const payload = {
        username,
        password
    };

    return axios.post("/app/authenticate", payload).then(handleResponse)
}

function signup(user, code) {

    var url
    if (code === undefined) {
        url = `/app/user/signup`
    } else {
        url = '/app/user/signup/' + code
    }

    return axios.post(url, user).then(handleResponse);
}

function forgotPassword(email) {
    return axios.post("/app/user/reset", {email}).then(handleResponse);
}

function confirmEmail(code) {
    return axios.get(`/app/user/confirm/` + code, {
        headers: {'Content-Type': 'application/json'},
        data: {}
    }).then(handleResponse);
}

function forgotPasswordCheck(code) {
    return axios.get(`/app/user/reset/` + code, {
        headers: {'Content-Type': 'application/json'},
        data: {}
    }).then(handleResponse);
}
function changePassword(password, new_password)
{
    return axios.post(`/app/user/password`, {password, new_password}).then(handleResponse);
}

function forgotPasswordConfirm(code, password) {
    return axios.post(`/app/user/reset/` + code, {password}).then(handleResponse);
}

function fetchSettings() {
    return axios.get("/app/user/settings", {
        headers: {'Content-Type': 'application/json'},
        data: {}
    })
    .then(handleResponse)
    .then(result => {
        return result.data.form;
    });
}

function updateSettings(user) {
    return axios.post("/app/user/settings", user).then(handleResponse);
}

function invite(email, role) {
    return axios.post("/app/user/invite", {email, role}).then(handleResponse);
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