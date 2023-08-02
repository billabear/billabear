import {createRouter, createWebHistory} from "vue-router";
import PayView from "../views/Invoice/PayView.vue";
import ErrorView from "../views/ErrorView.vue";

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            name: "app.home",
            path: "/portal/pay/:hash",
            component: PayView,
        },
        {
            name: "public.error",
            path: "/portal/error",
            component: ErrorView,
        },
        // otherwise redirect to home
        { path: '/:pathMatch(.*)/', redirect: '/portal/error' }
    ]
})