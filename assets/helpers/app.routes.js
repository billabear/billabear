import Dashboard from "../views/App/Dashboard";
import TeamSettings from "../views/App/TeamSettings";
import Plan from "../views/App/Plan";
import UserSettings from "../views/App/User/UserSettings";
import UserInvite from "../views/App/User/UserInvite";
import Billing from "../views/App/Billing/Billing";
import BillingAddress from "../views/App/Billing/BillingAddress";
import BillingMethods from "../views/App/Billing/BillingMethods";

// All paths have the prefix /app/.
export const APP_ROUTES = [
    {
        name: "app.home",
        path: "home",
        component: Dashboard,
    },
    {
        name: 'app.team',
        path: "team",
        component: TeamSettings,
    },
    {
        name: 'app.plan',
        path: "plan",
        component: Plan
    },
    {
        name: 'app.user.settings',
        path: "user/settings",
        component: UserSettings,
    },
    {
        name: "app.user.invite",
        path: "user/invite",
        component: UserInvite,
    },
    {
        name: 'app.billing',
        path: 'billing',
        component: Billing,
        children: [
            {
                name: 'app.billing.details',
                path: '',
                component: BillingAddress
            },
            {
                name: 'app.billing.methods',
                path: 'methods',
                component: BillingMethods,
            }
        ]
    }
]
