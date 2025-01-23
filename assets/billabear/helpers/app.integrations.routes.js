import PaymentList from "../views/App/Payments/PaymentList.vue";
import PaymentView from "../views/App/Payments/PaymentView.vue";
import RefundList from "../views/App/Refund/RefundList.vue";
import RefundView from "../views/App/Refund/RefundView.vue";
import ChargeBacksList from "../views/App/ChargeBacks/ChargeBacksList.vue";
import CheckoutList from "../views/App/Checkout/CheckoutList.vue";
import CheckoutCreate from "../views/App/Checkout/CheckoutCreate.vue";
import CheckoutView from "../views/App/Checkout/CheckoutView.vue";
import CountryList from "../views/App/Country/CountryList.vue";
import CountryCreate from "../views/App/Country/CountryCreate.vue";
import CountryView from "../views/App/Country/CountryView.vue";
import CountryEdit from "../views/App/Country/CountryEdit.vue";
import TaxTypeList from "../views/App/TaxType/TaxTypeList.vue";
import TaxTypeCreate from "../views/App/TaxType/TaxTypeCreate.vue";
import StateView from "../views/App/Country/StateView.vue";
import StateEdit from "../views/App/Country/StateEdit.vue";
import StateCreate from "../views/App/Country/StateCreate.vue";
import TaxTypeUpdate from "../views/App/TaxType/TaxTypeUpdate.vue";
import FinanceIntegration from "../views/App/Integrations/FinanceIntegration.vue";
import CustomerSupportIntegrations from "../views/App/Integrations/CustomerSupportIntegrations.vue";
import NewsletterIntegrations from "../views/App/Integrations/NewsletterIntegrations.vue";

export const AppIntegrationsRoutes  = [
    {
        name: 'app.integrations.accounting',
        path: 'integrations/accounting',
        component: FinanceIntegration
    },
    {
        name: 'app.integrations.customer_support',
        path: 'integrations/customer-support',
        component: CustomerSupportIntegrations
    },
    {
        name: 'app.integrations.newsletter',
        path: 'integrations/newsletter',
        component: NewsletterIntegrations,
    },
];
