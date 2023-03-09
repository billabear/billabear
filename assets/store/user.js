import { router } from "../helpers/router";
import { userservice } from "../services/userservice";

const rawUserData = localStorage.getItem('user');
var userData;
try {
     userData = JSON.parse(rawUserData);
} catch (e) {
     userData = null;
}

const state = {
    logged_in: (userData !== null),
    status: null,
    error_info: {
        has_error: false,
        message: undefined,
    },
    user: userData,
    redirect_page: undefined,
    in_progress: false,
    successfully_progress: false,
}

const actions = {
    login({ dispatch, commit }, { username, password }) {

        commit('loginRequest', { username });

        if (username === "") {
            commit('loginFailure', "A username must be provided");
            return;
        }

        if (password === "") {
            commit('loginFailure', "A password must be provided");
            return;
        }

        userservice.login(username, password)
            .then(
                user => {
                    commit('loginSuccess', user.data);
                    localStorage.setItem('user', JSON.stringify(user.data));

                    const url = localStorage.getItem('app_redirect')
                    if (url === null) {
                        router.push('/app/home');
                    } else {
                        localStorage.removeItem('app_redirect')
                        router.push(url)
                    }
                },
                error => {
                    commit('loginFailure', error);
                }
            );
    },
    resetPassword({ dispatch, commit }, { password }) {

    },
    logout({ commit }) {
        commit('logout');
    },
    signup({ dispatch, commit }, user) {
        commit('signupRequest', user);
    },
    markAsLoggedin({commit}, {user}) {
        localStorage.setItem('user', JSON.stringify(user));
        commit('loginSuccess', user);
    }

};

const mutations = {
    loginRequest(state, user) {
        state.in_progress = true;
        state.user = user;
        state.error_info = {
            has_error: false,
            message: undefined,
        }
    },
    loginSuccess(state, user) {
        state.in_progress = false;
        state.status = { loggedIn: true };
        state.user = user;
        state.successfully_progress = true;
    },
    loginFailure(state, error) {
        state.in_progress = false;
        state.status = {error: true};
        state.error_info = {
            has_error: true,
            message: error
        }
        state.user = null;
    },
    resetPasswordRequest(state) {
        state.in_progress = true;
    }
};

export const userStore = {
    namespaced: true,
    state,
    actions,
    mutations
};