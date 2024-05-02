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

export const AppFinanceRoutes  = [
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
    },
    {
        name: 'app.system.country.list',
        path: 'country/list',
        component: CountryList
    },
    {
        name: 'app.system.country.create',
        path: 'country/create',
        component: CountryCreate
    },
    {
        name: 'app.system.country.view',
        path: 'country/:id/view',
        component: CountryView
    },
    {
        name: 'app.system.country.edit',
        path: 'country/:id/edit',
        component: CountryEdit
    },
    {
        name: 'app.system.tax_type.list',
        path: 'tax/type/list',
        component: TaxTypeList
    },
    {
        name: 'app.system.tax_type.create',
        path: 'tax/type/create',
        component: TaxTypeCreate
    }
];
