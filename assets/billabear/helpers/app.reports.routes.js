import Dashboard from "../views/App/Dashboard.vue";
import ExpiringCardsList from "../views/App/Reports/ExpiringCards/ExpiringCardsList.vue";
import SubscriptionsOverview from "../views/App/Reports/Subscriptions/SubscriptionsOverview.vue";
import SubscriptionsChurn from "../views/App/Reports/Subscriptions/SubscriptionsChurn.vue";
import SubscriptionsNewStats from "../views/App/Reports/Subscriptions/SubscriptionsNewStats.vue";
import LifetimeReport from "../views/App/Reports/Financial/LifetimeReport.vue";
import TaxReportDashboard from "../views/App/Reports/Tax/TaxReportDashboard.vue";

export const REPORT_ROUTES = [
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
        name: 'app.report.tax',
        path: 'tax',
        component: TaxReportDashboard,

    },
    {
        name: 'app.report.churn',
        path: 'churn',
        component: SubscriptionsChurn,
    },
    {
        name: 'app.report.subscription_stats',
        path: 'subscription-stats',
        component: SubscriptionsNewStats,
    },
    {
        name: 'app.report.lifetime',
        path: 'lifetime',
        component: LifetimeReport,
    }
];
