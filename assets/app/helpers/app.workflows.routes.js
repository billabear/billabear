import CancellationRequestList from "../views/App/Workflows/CancellationRequest/CancellationRequestList.vue";
import CancellationRequestView from "../views/App/Workflows/CancellationRequest/CancellationRequestView.vue";
import SubscriptionCreationList from "../views/App/Workflows/SubscriptionCreation/SubscriptionCreationList.vue";
import SubscriptionCreationView from "../views/App/Workflows/SubscriptionCreation/SubscriptionCreationView.vue";


export const WORKFLOWS_ROUTES = [
    {
        name: 'app.workflows.cancellation_request.list',
        path: 'cancellation-request/list',
        component: CancellationRequestList
    },
    {
        name: 'app.workflows.cancellation_request.view',
        path: 'cancellation-request/:id/view',
        component: CancellationRequestView
    },
    {
        name: 'app.workflows.subscription_creation.list',
        path: 'subscription-creation/list',
        component: SubscriptionCreationList
    },
    {
        name: 'app.workflows.subscription_creation.view',
        path: 'subscription-creation/:id/view',
        component: SubscriptionCreationView
    },
];