import Dashboard from "../views/App/Dashboard";
import TeamSettings from "../views/App/TeamSettings";
import Plan from "../views/App/Plan";
import UserSettings from "../views/App/User/UserSettings";
import UserInvite from "../views/App/User/UserInvite";
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
import SubscriptionPlanCreate from "../views/App/SubscriptionPlan/SubscriptionPlanCreate.vue";
import SubscriptionPlanView from "../views/App/SubscriptionPlan/SubscriptionPlanView.vue";
import SubscriptionPlanUpdate from "../views/App/SubscriptionPlan/SubscriptionPlanUpdate.vue";
import AddPaymentDetails from "../views/App/PaymentDetails/AddPaymentDetails.vue";
import SubscriptionCreate from "../views/App/Subscription/SubscriptionCreate.vue";
import SubscriptionView from "../views/App/Subscription/SubscriptionView.vue";
import SubscriptionList from "../views/App/Subscription/SubscriptionList.vue";
import PaymentList from "../views/App/Payments/PaymentList.vue";
import PaymentView from "../views/App/Payments/PaymentView.vue";
import RefundView from "../views/App/Refund/RefundView.vue";
import RefundList from "../views/App/Refund/RefundList.vue";
import TransactionView from "../views/App/transactions/TransactionView.vue";
import SettingsGroup from "../views/App/Settings/SettingsGroup.vue";
import SubscriptionGroup from "../views/App/Subscription/SubscriptionGroup.vue";
import ProductGroup from "../views/App/Product/ProductGroup.vue";
import CustomerGroup from "../views/App/Customer/CustomerGroup.vue";
import TemplateList from "../views/App/Settings/PdfTemplates/PdfTemplateList.vue";
import TemplateUpdate from "../views/App/Settings/PdfTemplates/PdfTemplateUpdate.vue";
import BrandSettingsList from "../views/App/Settings/BrandSettings/BrandSettingsList.vue";
import BrandSettingsUpdate from "../views/App/Settings/BrandSettings/BrandSettingsUpdate.vue";
import BrandSettingsCreate from "../views/App/Settings/BrandSettings/BrandSettingsCreate.vue";
import PdfTemplateList from "../views/App/Settings/PdfTemplates/PdfTemplateList.vue";
import pdfTemplateUpdate from "../views/App/Settings/PdfTemplates/PdfTemplateUpdate.vue";
import PdfTemplateUpdate from "../views/App/Settings/PdfTemplates/PdfTemplateUpdate.vue";
import EmailTemplateList from "../views/App/Settings/EmailTemplates/EmailTemplateList.vue";
import EmailTemplateCreate from "../views/App/Settings/EmailTemplates/EmailTemplateCreate.vue";
import EmailTemplateUpdate from "../views/App/Settings/EmailTemplates/EmailTemplateUpdate.vue";
import NotificationSettingsUpdate from "../views/App/Settings/NotificationSettings/NotificationSettingsUpdate.vue";

// All paths have the prefix /app/.
export const APP_ROUTES = [
    {
        name: "app.home",
        path: "home",
        component: Dashboard,
    },
    {
        name: 'app.customer',
        path: 'customers',
        component: CustomerGroup,
        children: [
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
                name: 'app.customer.payment_details.add',
                path: 'customer/:customerId/payment-details/add',
                component: AddPaymentDetails
            },
            {
                name: 'app.subscription.create',
                path: 'customer/:customerId/subscription/add',
                component: SubscriptionCreate
            },
        ]
    },
    {
        name: 'app.settings',
        path: 'settings',
        component: SettingsGroup,
        children: [
            {
                name: 'app.user.settings',
                path: "user",
                component: UserSettings,
            },
            {
                name: "app.user.invite",
                path: "user/invite",
                component: UserInvite,
            },
            {
                name: "app.settings.pdf_template.list",
                path: "templates/list",
                component: PdfTemplateList
            },
            {
                name: "app.settings.pdf_template.update",
                path: "templates/update/:id",
                component: PdfTemplateUpdate
            },
            {
                name: "app.settings.brand_settings.list",
                path: "brand-settings",
                component: BrandSettingsList
            },
            {
                name: "app.settings.brand_settings.update",
                path: "brand-settings/:id",
                component: BrandSettingsUpdate
            },
            {
                name: "app.settings.brand_settings.create",
                path: "brand-settings/new",
                component: BrandSettingsCreate
            },
            {
                name: "app.settings.email_template.list",
                path: "email-template/list",
                component: EmailTemplateList
            },
            {
                name: "app.settings.email_template.create",
                path: "email-template/create",
                component: EmailTemplateCreate
            },
            {
                name: "app.settings.email_template.update",
                path: "email-template/:id/update",
                component: EmailTemplateUpdate
            },
            {
                name: "app.settings.notification_settings.update",
                path: "notification-settings/update",
                component: NotificationSettingsUpdate
            }
        ]
    },
    {
        name: 'app.product',
        path: 'product',
        component: ProductGroup,
        children: [
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
            },]
    },
    {
        name: 'app.subscription',
        path: 'subscriptions',
        component: SubscriptionGroup,
        children: [
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
        ]
    },
    {
        name: 'app.transactions',
        path: 'transactions',
        component: TransactionView,
        children: [
            {
                name: 'app.payment.dummy',
                path: '',
                component: PaymentList
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
                name: 'app.refund.list',
                path: 'refunds/list',
                component: RefundList
            },
            {
                name: 'app.refund.view',
                path: 'refunds/view/:id',
                component: RefundView
            },
        ]
    }
]
