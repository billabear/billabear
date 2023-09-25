import SubscriptionView from "../views/App/Subscription/SubscriptionView.vue";
import SubscriptionList from "../views/App/Subscription/SubscriptionList.vue";
import MassChangeList from "../views/App/Subscription/MassChange/MassChangeList.vue";
import MassChangeCreate from "../views/App/Subscription/MassChange/MassChangeCreate.vue";

export const AppSubscriptionsRoutes  = [
    {
        name: 'app.subscription.view',
        path: 'subscription/:subscriptionId',
        component: SubscriptionView
    },
    {
        name: 'app.subscription.list',
        path: '',
        component: SubscriptionList
    },
    {
        name: 'app.subscription.mass_change.list',
        path: 'mass-change',
        component: MassChangeList,
    },
    {
        name: 'app.subscription.mass_change.create',
        path: 'mass-change/create',
        component: MassChangeCreate,
    },
]