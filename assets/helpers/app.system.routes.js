import WebhookMain from "../views/App/System/Webhook/WebhookMain.vue";
import WebhookEndpointList from "../views/App/System/Webhook/WebhookEndpoint/WebhookEndpointList.vue";


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
    }
]