import axios from "axios";
import { AuthService } from "./auth";
import { router } from "../helpers/router";

// Handle redirections when logged out.
export const setupAxiosInterceptors = () => {
    axios.interceptors.response.use(response => {
        return response;
    }, error => {
        if (error.response.status === 401) {
            AuthService.logout();
            AuthService.setRedirect(window.location.pathname);
            router.push('/login');
        }
        return Promise.reject(error);
    });
};