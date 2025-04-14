import {createRouter, createWebHistory} from "vue-router";
import PayView from "../views/Invoice/PayView.vue";
import ErrorView from "../views/ErrorView.vue";
import QuotePayView from "../views/Quote/QuotePayView.vue";
import CheckoutView from "../views/Checkout/CheckoutView.vue";
import CustomerManage from "../views/Customer/CustomerManage.vue";

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            name: "app.home",
            path: "/portal/pay/:hash",
            component: PayView,
        },
        {
            name: "public.quote.pay",
            path: "/portal/quote/:hash",
            component: QuotePayView,
        },
        {
            name: "public.checkout.pay",
            path: "/portal/checkout/:slug",
            component: CheckoutView,
        },
        {
            name: "public.customer.manage",
            path: "/portal/customer/:token",
            component: CustomerManage,
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