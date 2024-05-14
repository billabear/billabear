import {createRouter, createWebHistory} from 'vue-router'

import axios from "axios";
import Login from "../views/Public/Login.vue";
import Signup from "../views/Public/Signup.vue";
import ForgotPassword from "../views/Public/ForgotPassword.vue";
import ForgotPasswordConfirm from "../views/Public/ForgotPasswordConfirm.vue";
import ConfirmEmail from "../views/Public/ConfirmEmail.vue";
import {APP_ROUTES} from "./app.routes";
import InternalApp from "../views/App/InternalApp.vue";
import StartingPoint from "../views/Install/StartingPoint.vue";
import StripeNoKey from "../views/Install/StripeNoKey.vue";
import StripeInvalid from "../views/Install/StripeInvalid.vue";


export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', name: 'public.index', component: Login },
        { path: '/login', name: 'public.login', component: Login },
        { path: '/signup', name: 'public.signup', component: Signup },
        { path: '/signup/:code', name: 'public.signup_invite', component: Signup },
        { path: '/forgot-password', name: 'public.forgot_password', component: ForgotPassword },
        { path: '/forgot-password/:code', name: 'public.forgot_password_confirm', component: ForgotPasswordConfirm },
        { path: '/confirm-email/:code', name: 'public.confirm_email', component: ConfirmEmail },
        {
            path: '/site/',
            component: InternalApp,
            children: APP_ROUTES,
        },
        {
            path: '/install', component: StartingPoint
        },
        {
            path: '/error/stripe', component: StripeNoKey
        },
        {
            path: '/error/stripe-invalid', component: StripeInvalid
        },
        // otherwise redirect to home
        { path: '/:pathMatch(.*)/', redirect: '/login' }
    ]
});

router.beforeEach((to, from, next) => {
    // redirect to login page if not logged in and trying to access a restricted page
    const publicPages = ['/login', '/signup', '/signup/:code', '/forgot-password', '/forgot-password/:code', '/confirm-email/:code', '/', '/install', '/error/stripe-invalid', '/error/stripe'];
    const authRequired = !publicPages.includes(to.matched[0].path);
    var loggedIn = localStorage.getItem('user');

    if (loggedIn == 'undefined'){
        loggedIn = null;
        localStorage.getItem('user', null);
    }

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
    return Promise.reject(error);
});