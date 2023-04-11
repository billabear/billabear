import Dashboard from "../views/App/Dashboard";
import TeamSettings from "../views/App/TeamSettings";
import Plan from "../views/App/Plan";
import UserSettings from "../views/App/User/UserSettings";
import UserInvite from "../views/App/User/UserInvite";
import Billing from "../views/App/Billing/Billing";
import BillingAddress from "../views/App/Billing/BillingAddress";
import BillingMethods from "../views/App/Billing/BillingMethods";
import CustomerList from "../views/App/Customer/CustomerList.vue";
import CustomerCreate from "../views/App/Customer/CustomerCreate.vue";
import CustomerView from "../views/App/Customer/CustomerView.vue";
import CustomerUpdate from "../views/App/Customer/CustomerUpdate.vue";
import ProductList from "../views/App/Product/ProductList.vue";
import ProductUpdate from "../views/App/Product/ProductUpdate.vue";
import ProductView from "../views/App/Product/ProductView.vue";
import ProductCreate from "../views/App/Product/ProductCreate.vue";
import PriceCreate from "../views/App/Price/PriceCreate.vue";
import FeatureList from "../views/App/Feature/FeatureList.vue";
import FeatureCreate from "../views/App/Feature/FeatureCreate.vue";
import {SUBCLASSING} from "core-js/internals/promise-constructor-detection";
import SubscriptionPlanCreate from "../views/App/SubscriptionPlan/SubscriptionPlanCreate.vue";
import SubscriptionPlanView from "../views/App/SubscriptionPlan/SubscriptionPlanView.vue";
import SubscriptionPlanUpdate from "../views/App/SubscriptionPlan/SubscriptionPlanUpdate.vue";
import AddPaymentDetails from "../views/App/PaymentDetails/AddPaymentDetails.vue";
import SubscriptionCreate from "../views/App/Subscription/SubscriptionCreate.vue";
import SubscriptionView from "../views/App/Subscription/SubscriptionView.vue";
import SubscriptionList from "../views/App/Subscription/SubscriptionList.vue";
import PaymentList from "../views/App/Payments/PaymentList.vue";
import PaymentView from "../views/App/Payments/PaymentView.vue";

// All paths have the prefix /app/.
export const APP_ROUTES = [
    {
        name: "app.home",
        path: "home",
        component: Dashboard,
    },
    {
        name: 'app.team',
        path: "team",
        component: TeamSettings,
    },
    {
        name: 'app.plan',
        path: "plan",
        component: Plan
    },
    {
        name: 'app.user.settings',
        path: "user/settings",
        component: UserSettings,
    },
    {
        name: "app.user.invite",
        path: "user/invite",
        component: UserInvite,
    },
    {
        name: 'app.customer.list',
        path: 'customer/list',
        component: CustomerList
    },
    {
        name: 'app.customer.create',
        path: 'customer/create',
        component: CustomerCreate,
    },
    {
        name: 'app.customer.view',
        path: 'customer/view/:id',
        component: CustomerView
    },
    {
        name: 'app.customer.update',
        path: 'customer/update/:id',
        component: CustomerUpdate
    },
    {
        name: 'app.payment.list',
        path: 'payments/list',
        component: PaymentList
    },
    {
        name: 'app.payment.view',
        path: 'payments/view/:id',
        component: PaymentView
    },
    {
        name: 'app.customer.payment_details.add',
        path: 'customer/:customerId/payment-details/add',
        component: AddPaymentDetails
    },
    {
        name: 'app.feature.list',
        path: 'feature/list',
        component: FeatureList
    },
    {
        name: 'app.feature.create',
        path: 'feature/create',
        component: FeatureCreate
    },
    {
        name: 'app.product.list',
        path: 'product/list',
        component: ProductList
    },
    {
        name: 'app.product.create',
        path: 'product/create',
        component: ProductCreate,
    },
    {
        name: 'app.product.view',
        path: 'product/view/:id',
        component: ProductView
    },
    {
        name: 'app.product.update',
        path: 'product/update/:id',
        component: ProductUpdate
    },
    {
        name: 'app.subscription.create',
        path: 'customer/:customerId/subscription/add',
        component: SubscriptionCreate
    },
    {
        name: 'app.subscription.view',
        path: 'subscription/:subscriptionId',
        component: SubscriptionView
    },
    {
        name: 'app.subscription.list',
        path: 'subscription',
        component: SubscriptionList
    },
    {
        name: 'app.price.create',
        path: 'product/:productId/price/create',
        component: PriceCreate,
    },
    {
        name: 'app.subscription_plan.create',
        path: 'product/:productId/subscription-plan/create',
        component: SubscriptionPlanCreate,
    },
    {
        name: 'app.subscription_plan.view',
        path: 'product/:productId/subscription-plan/view/:subscriptionPlanId',
        component: SubscriptionPlanView,
    },
    {
        name: 'app.subscription_plan.update',
        path: 'product/:productId/subscription-plan/update/:subscriptionPlanId',
        component: SubscriptionPlanUpdate,
    },
    {
        name: 'app.billing',
        path: 'billing',
        component: Billing,
        children: [
            {
                name: 'app.billing.details',
                path: '',
                component: BillingAddress
            },
            {
                name: 'app.billing.methods',
                path: 'methods',
                component: BillingMethods,
            }
        ]
    }
]
