import { defineStore } from 'pinia';
import { router } from "../helpers/router";
import { userservice } from "../services/userservice";
import { LocalStorageService } from "../services/localStorageService";

export const useUserStore = defineStore('user', {
    state: () => {
        const userData = LocalStorageService.getUserData();
        
        return {
            logged_in: userData !== null,
            status: null,
            error_info: {
                has_error: false,
                message: undefined,
            },
            user: userData,
            redirect_page: undefined,
            in_progress: false,
            successfully_progress: false,
            locale: userData?.locale || 'en'
        };
    },

    actions: {
        async login({ username, password }) {
            this.loginRequest({ username });

            if (username === "") {
                this.loginFailure("A username must be provided");
                return;
            }

            if (password === "") {
                this.loginFailure("A password must be provided");
                return;
            }

            try {
                const user = await userservice.login(username, password);
                this.loginSuccess(user.data);
                LocalStorageService.setUserData(user.data);

                const url = LocalStorageService.getAppRedirect();
                if (url === null) {
                    router.push('/site/home');
                } else {
                    LocalStorageService.removeAppRedirect();
                    router.push(url);
                }
            } catch (error) {
                this.loginFailure(error.response.data.error);
            }
        },

        async resetPassword({ password }) {
            // TODO: Implement password reset functionality
            console.warn('resetPassword action not implemented yet');
            
            this.resetPasswordRequest();
            try {
                // When implemented, this should call userservice.resetPassword(password)
                // await userservice.resetPassword(password);
                throw new Error('Password reset functionality not implemented');
            } catch (error) {
                console.error('Password reset error:', error);
                this.in_progress = false;
            }
        },

        logout() {
            this.logoutUser();
        },

        signup(user) {
            this.signupRequest(user);
        },

        markAsLoggedin({ user }) {
            LocalStorageService.setUserData(user);
            this.loginSuccess(user);
        },

        updateLocale({ locale }) {
            this.setLocale(locale);
        },

        // Mutation-like methods (now just regular methods in Pinia)
        loginRequest(user) {
            this.in_progress = true;
            this.user = user;
            this.error_info = {
                has_error: false,
                message: undefined,
            };
        },

        setLocale(locale) {
            this.locale = locale;
        },

        loginSuccess(user) {
            this.in_progress = false;
            this.status = { loggedIn: true };
            this.user = user;
            this.successfully_progress = true;
            this.locale = user.locale;
            this.logged_in = true;
        },

        loginFailure(error) {
            this.in_progress = false;
            this.status = { error: true };
            this.error_info = {
                has_error: true,
                message: error
            };
            this.user = null;
            this.logged_in = false;
        },

        resetPasswordRequest() {
            this.in_progress = true;
        },

        logoutUser() {
            this.user = null;
            this.logged_in = false;
            this.status = null;
            this.error_info = {
                has_error: false,
                message: undefined,
            };
            LocalStorageService.removeUserData();
        },

        signupRequest(user) {
            // Handle signup request logic here
            console.log('Signup request for user:', user);
        }
    }
});
