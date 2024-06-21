import WebhookMain from "../views/App/System/Webhook/WebhookMain.vue";
import WebhookEndpointList from "../views/App/System/Webhook/WebhookEndpoint/WebhookEndpointList.vue";
import WebhookEndpointCreate from "../views/App/System/Webhook/WebhookEndpoint/WebhookEndpointCreate.vue";
import WebhookEndpointView from "../views/App/System/Webhook/WebhookEndpoint/WebhookEndpointView.vue";
import WebhookEventView from "../views/App/System/Webhook/WebhookEvent/WebhookEventView.vue";
import CancellationRequestList from "../views/App/Workflows/CancellationRequest/CancellationRequestList.vue";
import CancellationRequestView from "../views/App/Workflows/CancellationRequest/CancellationRequestView.vue";
import IntegrationsList from "../views/App/System/Integrations/IntegrationsList.vue";
import SlackWebhookList from "../views/App/System/Integrations/Slack/SlackWebhookList.vue";
import SlackWebhookCreate from "../views/App/System/Integrations/Slack/SlackWebhookCreate.vue";
import SlackGroup from "../views/App/System/Integrations/Slack/SlackGroup.vue";
import SlackNotificationList from "../views/App/System/Integrations/Slack/SlackNotificationList.vue";
import SlackNotificationCreate from "../views/App/System/Integrations/Slack/SlackNotificationCreate.vue";


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

];
