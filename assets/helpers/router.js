import {createRouter, createWebHistory} from 'vue-router'

import axios from "axios";
import Login from "../views/Public/Login";
import Signup from "../views/Public/Signup";
import ForgotPassword from "../views/Public/ForgotPassword";
import ForgotPasswordConfirm from "../views/Public/ForgotPasswordConfirm";
import ConfirmEmail from "../views/Public/ConfirmEmail";
import {APP_ROUTES} from "./app.routes";
import InternalApp from "../views/App/InternalApp";


export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login', name: 'public.login', component: Login },
        { path: '/signup', name: 'public.signup', component: Signup },
        { path: '/forgot-password', name: 'public.forgot_password', component: ForgotPassword },
        { path: '/forgot-password/:code', name: 'public.forgot_password_confirm', component: ForgotPasswordConfirm },
        { path: '/confirm-email/:code', name: 'public.confirm_email', component: ConfirmEmail },
        {
            path: '/app/',
            component: InternalApp,
            children: APP_ROUTES,
        },
        // otherwise redirect to home
        { path: '/:pathMatch(.*)/', redirect: '/login' }
    ]
});

router.beforeEach((to, from, next) => {
    // redirect to login page if not logged in and trying to access a restricted page
    const publicPages = ['/login', '/signup', '/signup/:code', '/forgot-password', '/forgot-password/:code', '/confirm-email/:code', '/'];
    const authRequired = !publicPages.includes(to.matched[0].path);
    const loggedIn = localStorage.getItem('user');


    if (authRequired && !loggedIn) {
        return next('/login');
    }

    next();
})

// Handle redirections when logged out.
axios.interceptors.response.use(response => {
    return response;
}, error => {
    if (error.response.status === 401) {
        localStorage.setItem('user', null);
        router.push('/login')
    }
    return error;
});