import {createRouter, createWebHistory} from "vue-router";
import PayView from "../views/Invoice/PayView.vue";
import ErrorView from "../views/ErrorView.vue";

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            name: "app.home",
            path: "/public/home",
            component: PayView,
        },
        {
            name: "public.error",
            path: "/public/error",
            component: ErrorView,
        },
        // otherwise redirect to home
        { path: '/:pathMatch(.*)/', redirect: '/public/error' }
    ]
})