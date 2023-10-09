import CancellationRequestList from "../views/App/Workflows/CancellationRequest/CancellationRequestList.vue";
import CancellationRequestView from "../views/App/Workflows/CancellationRequest/CancellationRequestView.vue";
import SubscriptionCreationList from "../views/App/Workflows/SubscriptionCreation/SubscriptionCreationList.vue";
import SubscriptionCreationView from "../views/App/Workflows/SubscriptionCreation/SubscriptionCreationView.vue";
import PaymentCreationView from "../views/App/Workflows/PaymentCreation/PaymentCreationView.vue";
import PaymentCreationList from "../views/App/Workflows/PaymentCreation/PaymentCreationList.vue";
import RefundCreatedProcessList from "../views/App/Workflows/RefundCreatedProcess/RefundCreatedProcessList.vue";
import RefundCreatedProcessView from "../views/App/Workflows/RefundCreatedProcess/RefundCreatedProcessView.vue";
import PaymentFailureProcessList from "../views/App/Workflows/PaymentFailureProcess/PaymentFailureProcessList.vue";
import PaymentFailureProcessView from "../views/App/Workflows/PaymentFailureProcess/PaymentFailureProcessView.vue";


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
    {
        name: 'app.workflows.payment_creation.list',
        path: 'payment-creation/list',
        component: PaymentCreationList
    },
    {
        name: 'app.workflows.payment_creation.view',
        path: 'payment-creation/:id/view',
        component: PaymentCreationView
    },
    {
        name: 'app.workflows.refund_created_process.list',
        path: 'refund-created-process/list',
        component: RefundCreatedProcessList
    },
    {
        name: 'app.workflows.refund_created_process.view',
        path: 'refund-created-process/:id/view',
        component: RefundCreatedProcessView
    },
    {
        name: 'app.workflows.payment_failure_process.list',
        path: 'payment-failure-process/list',
        component: PaymentFailureProcessList
    },
    {
        name: 'app.workflows.payment_failure_process.view',
        path: 'payment-failure-process/:id/view',
        component: PaymentFailureProcessView
    }
];