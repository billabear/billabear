import WebhookMain from "../views/App/System/Webhook/WebhookMain.vue";
import WebhookEndpointList from "../views/App/System/Webhook/WebhookEndpoint/WebhookEndpointList.vue";
import WebhookEndpointCreate from "../views/App/System/Webhook/WebhookEndpoint/WebhookEndpointCreate.vue";
import WebhookEndpointView from "../views/App/System/Webhook/WebhookEndpoint/WebhookEndpointView.vue";
import WebhookEventView from "../views/App/System/Webhook/WebhookEvent/WebhookEventView.vue";
import CancellationRequestList from "../views/App/Workflows/CancellationRequest/CancellationRequestList.vue";
import CancellationRequestView from "../views/App/Workflows/CancellationRequest/CancellationRequestView.vue";
import CountryList from "../views/App/Country/CountryList.vue";
import CountryCreate from "../views/App/Country/CountryCreate.vue";
import CountryView from "../views/App/Country/CountryView.vue";
import CountryEdit from "../views/App/Country/CountryEdit.vue";
import TaxTypeList from "../views/App/TaxType/TaxTypeList.vue";
import TaxTypeCreate from "../views/App/TaxType/TaxTypeCreate.vue";


export const SYSTEM_ROUTES = [
    {
        name: 'app.system.webhooks',
        path: 'webhooks',
        component: WebhookMain
    },
    {
        name: 'app.system.webhook_endpoints.list',
        path: 'webhook/endpoints/list',
        component: WebhookEndpointList
    },
    {
        name: 'app.system.webhook_endpoints.create',
        path: 'webhook/endpoints/new',
        component: WebhookEndpointCreate
    },
    {
        name: 'app.system.webhook_endpoints.view',
        path: 'webhook/endpoints/:id/view',
        component: WebhookEndpointView
    },
    {
        name: 'app.system.webhook_event.view',
        path: 'webhook/event/:id/view',
        component: WebhookEventView
    },
    {
        name: 'app.system.cancellation_request.list',
        path: 'cancellation-request/list',
        component: CancellationRequestList
    },
    {
        name: 'app.system.cancellation_request.view',
        path: 'cancellation-request/:id/view',
        component: CancellationRequestView
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