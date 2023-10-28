import Dashboard from "../views/App/Dashboard.vue";
import TeamSettings from "../views/App/TeamSettings.vue";
import Plan from "../views/App/Plan.vue";
import UserSettings from "../views/App/User/UserSettings.vue";
import UserInvite from "../views/App/User/UserInvite.vue";
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
import BrandSettingsList from "../views/App/Settings/BrandSettings/BrandSettingsList.vue";
import BrandSettingsUpdate from "../views/App/Settings/BrandSettings/BrandSettingsUpdate.vue";
import BrandSettingsCreate from "../views/App/Settings/BrandSettings/BrandSettingsCreate.vue";
import PdfTemplateList from "../views/App/Settings/PdfTemplates/PdfTemplateList.vue";
import PdfTemplateUpdate from "../views/App/Settings/PdfTemplates/PdfTemplateUpdate.vue";
import EmailTemplateList from "../views/App/Settings/EmailTemplates/EmailTemplateList.vue";
import EmailTemplateCreate from "../views/App/Settings/EmailTemplates/EmailTemplateCreate.vue";
import EmailTemplateUpdate from "../views/App/Settings/EmailTemplates/EmailTemplateUpdate.vue";
import NotificationSettingsUpdate from "../views/App/Settings/NotificationSettings/NotificationSettingsUpdate.vue";
import ChargeBacksList from "../views/App/ChargeBacks/ChargeBacksList.vue";
import SystemSettingsUpdate from "../views/App/Settings/SystemSettings/SystemSettingsUpdate.vue";
import TeamUserList from "../views/App/Settings/Team/TeamUserList.vue";
import SettingsUserUpdate from "../views/App/Settings/Team/SettingsUserUpdate.vue";
import StripeImportList from "../views/App/Settings/Stripe/StripeImportList.vue";
import ApiKeysMain from "../views/App/Settings/ApiKeys/ApiKeysMain.vue";
import ReportsGroup from "../views/App/Reports/ReportsGroup.vue";
import ExpiringCardsList from "../views/App/Reports/ExpiringCards/ExpiringCardsList.vue";
import CreditCreate from "../views/App/Credit/CreditCreate.vue";
import InvoicesList from "../views/App/Invoices/InvoicesList.vue";
import StripeImportView from "../views/App/Settings/Stripe/StripeImportView.vue";
import UnpaidInvoicesList from "../views/App/Invoices/UnpaidInvoicesList.vue";
import ExchangeRatesList from "../views/App/Settings/ExchangeRates/ExchangeRatesList.vue";
import VouchersList from "../views/App/Vouchers/VouchersList.vue";
import VouchersCreate from "../views/App/Vouchers/VouchersCreate.vue";
import VouchersView from "../views/App/Vouchers/VouchersView.vue";
import SubscriptionsOverview from "../views/App/Reports/Subscriptions/SubscriptionsOverview.vue";
import VatOverview from "../views/App/Reports/Vat/VatOverview.vue";
import TaxSettings from "../views/App/Settings/TaxSettings/TaxSettings.vue";
import InvoiceGroup from "../views/App/Invoices/InvoiceGroup.vue";
import InvoiceCreate from "../views/App/Invoices/InvoiceCreate.vue";
import QuotesList from "../views/App/Quotes/QuotesList.vue";
import QuotesView from "../views/App/Quotes/QuotesView.vue";
import QuoteCreate from "../views/App/Quotes/QuoteCreate.vue";
import InvoicesView from "../views/App/Invoices/InvoicesView.vue";
import DeveloperGroup from "../views/App/System/SystemGroup.vue";
import {SYSTEM_ROUTES} from "./app.system.routes";
import SystemGroup from "../views/App/System/SystemGroup.vue";
import CheckoutCreate from "../views/App/Checkout/CheckoutCreate.vue";
import CheckoutView from "../views/App/Checkout/CheckoutView.vue";
import CheckoutList from "../views/App/Checkout/CheckoutList.vue";
import {AppSubscriptionsRoutes} from "./app.subscriptions.routes";
import SubscriptionsChurn from "../views/App/Reports/Subscriptions/SubscriptionsChurn.vue";
import {WORKFLOWS_ROUTES} from "./app.workflows.routes";
import WorkflowsGroup from "../views/App/Workflows/WorkflowsGroup.vue";
import PdfGeneratorSettings from "../views/App/Settings/PdfTemplates/PdfGeneratorSettings.vue";
import LifetimeReport from "../views/App/Reports/Financial/LifetimeReport.vue";

// All paths have the prefix /app/.
export const APP_ROUTES = [
    {
        name: "app.home",
        path: "home",
        component: Dashboard,
    },
    {
        name: "app.reports",
        path: "reports",
        redirect: {name: "app.report.subscriptions"},
        component: ReportsGroup,
        children: [
            {
                name: 'app.report.dashboard',
                path: '',
                component: Dashboard
            },
            {
                name: 'app.expiring_cards.list',
                path: 'expiring-cards',
                component: ExpiringCardsList
            },
            {
                name: 'app.report.subscriptions',
                path: 'subscriptions',
                component: SubscriptionsOverview,
            },
            {
                name: 'app.report.vat',
                path: 'vat',
                component: VatOverview,

            },
            {
                name: 'app.report.churn',
                path: 'churn',
                component: SubscriptionsChurn,
            },
            {
                name: 'app.report.lifetime',
                path: 'lifetime',
                component: LifetimeReport,
            }
        ]
    },
    {
        name: "app.system",
        path: "system",
        component: SystemGroup,
        children: SYSTEM_ROUTES
    },
    {
        name: "app.workflows",
        path: "workflows",
        redirect: {name: 'app.workflows.cancellation_request.list'},
        component: WorkflowsGroup,
        children: WORKFLOWS_ROUTES
    },
    {
        name: 'app.customer',
        path: 'customers',
        redirect: "customers",
        component: CustomerGroup,
        children: [
            {
                name: 'app.customer.list',
                path: '',
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
            {
                name: 'app.customer.credit.add',
                path: 'customer/:customerId/credit/add',
                component: CreditCreate
            }
        ]
    },

    {
        name: 'app.user.settings',
        path: "user",
        component: UserSettings,
    },
    {
        name: 'app.settings',
        path: 'settings',
        component: SettingsGroup,
        redirect: { name: "app.settings.pdf_template.list", },
        children: [
            {
                name: "app.user.invite",
                path: "users/invite",
                component: UserInvite,
            },
            {
                name: "app.settings.pdf_template.list",
                path: "templates/list",
                component: PdfTemplateList
            },
            {
                name: "app.settings.tax_settings.update",
                path: "tax-settings",
                component: TaxSettings
            },
            {
                name: "app.settings.pdf_template.update",
                path: "templates/update/:id",
                component: PdfTemplateUpdate
            },
            {
                name: "app.settings.pdf_template.generator",
                path: "templates/generator",
                component: PdfGeneratorSettings
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
            },
            {
                name: "app.settings.system_settings.update",
                path: "system/update",
                component: SystemSettingsUpdate
            },
            {
                name: "app.settings.users.list",
                path: "users/list",
                component: TeamUserList
            },
            {
                name: "app.settings.users.update",
                path: "users/:id/update",
                component: SettingsUserUpdate
            },
            {
                name: "app.settings.import.stripe",
                path: "import/stripe",
                component: StripeImportList,
            },
            {
                name: "app.settings.import.stripe.view",
                path: "import/stripe/:id/view",
                component: StripeImportView,
            },
            {
                name: "app.settings.api_keys.main",
                path: "api-keys",
                component: ApiKeysMain,
            },
            {
                name: "app.settings.exchange_rates.list",
                path: "exchange-rates",
                component: ExchangeRatesList
            }
        ]
    },
    {
        name: 'app.product',
        path: 'product',
        redirect: "product",
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
                path: 'list',
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
                name: 'app.vouchers.list',
                path: 'vouchers/list',
                component: VouchersList
            },
            {
                name: 'app.vouchers.create',
                path: 'vouchers/create',
                component: VouchersCreate
            },
            {
                name: 'app.vouchers.view',
                path: 'vouchers/view/:id',
                component: VouchersView
            },
        ]
    },
    {
        name: 'app.subscription',
        path: 'subscriptions',
        redirect: { name: "app.subscription.list", },
        component: SubscriptionGroup,
        children: AppSubscriptionsRoutes
    },
    {
        name: "app.invoices",
        path: "invoices",
        component: InvoiceGroup,
        children: [
            {
                name: 'app.invoices.list',
                path: "invoices/list",
                component: InvoicesList
            },
            {
                name: 'app.invoices.unpaid_list',
                path: "invoices/unpaid/list",
                component: UnpaidInvoicesList
            },
            {
                name: 'app.invoices.create',
                path: "invoices/create",
                component: InvoiceCreate,
            },
            {
                name: 'app.invoices.view',
                path: "invoices/view/:id",
                component: InvoicesView,
            },
            {
                name: 'app.quotes.create',
                path: "quotes/create",
                component: QuoteCreate,
            },
            {
                name: 'app.quotes.list',
                path: "quotes",
                component: QuotesList,
            },
            {
                name: 'app.quotes.view',
                path: "quotes/view/:id",
                component: QuotesView,
            }
        ]
    },
    {
        name: 'app.transactions',
        path: 'transactions',
        redirect: "transactions",
        component: TransactionView,
        children: [
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
            {
                name: 'app.charge_backs.list',
                path: 'charge-backs/list',
                component: ChargeBacksList
            },
            {
                name: 'app.checkout.list',
                path: 'checkout/list',
                component: CheckoutList
            },
            {
                name: 'app.checkout.create',
                path: 'checkout/create',
                component: CheckoutCreate
            },
            {
                name: 'app.checkout.view',
                path: 'checkout/view/:id',
                component: CheckoutView
            }
        ]
    }
]
