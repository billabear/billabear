import axios from "axios";

const state = {
    has_stripe_imports: true,
    has_stripe_key: false,
    has_product: false,
    has_subscription_plan: false,
    has_subscription: false,
    has_customer: false,
    show_onboarding: false,
    ready: false,
    error: false,
}

const actions = {
    stripeImport({commit}) {
        commit('markStripeImportDone');
    },
    setStripeImport({commit}, {defaultValue}) {
        commit('setStripeDefaultValue', defaultValue)
    },
    fetchData({commit}) {
        axios.get("/app/system/data").then((response) => {
            commit('setDefaults', response.data)
        }).catch(error => {
            commit('setError')
        })
    },
    dismissStripeImport({commit}) {
        axios.post("/app/settings/stripe-import/dismiss").then((response) => {
            commit('markStripeImportDone')
        })
    },
    stripeKeysAdded({commit}) {
        commit('markStripKeyAsDone');
    },
    productAdded({commit}){
        commit('markProductAsDone')
    },
    subscriptionPlanAdded({commit}) {
        commit('markSubscriptionPlanAsDone')
    },
    subscriptionAdded({commit}) {
        commit('markSubscriptionAsDone')
    },
    customerAdded({commit}) {
        commit('markCustomerAsDone')
    }
}

const mutations = {
    setError(state) {
        state.error = true;
    },
    setDefaults(state, defaults) {
        state.has_stripe_key = defaults.has_stripe_key;
        state.has_product = defaults.has_product;
        state.has_subscription_plan = defaults.has_subscription_plan;
        state.has_subscription = defaults.has_subscription;
        state.has_customer = defaults.has_customer;
        state.has_stripe_imports = defaults.has_stripe_imports;
        state.ready = true;

        state.show_onboarding = (
            !defaults.has_stripe_key || !defaults.has_product ||
            !defaults.has_subscription_plan || !defaults.has_stripe_imports ||
            !defaults.has_subscription || !defaults.has_customer
        );
    },
    markStripKeyAsDone(state) {
        state.has_stripe_key = true;
    },
    markProductAsDone(state) {
        state.has_product = true;
    },
    markSubscriptionAsDone(state) {
        state.has_subscription = true;
    },
    markCustomerAsDone(state) {
        state.has_customer = true;
    },
    markSubscriptionPlanAsDone(state) {
        state.has_subscription_plan = true;
    },
    markStripeImportDone(state) {
        state.has_stripe_imports = true;
    },
    setStripeDefaultValue(state, defaultValue) {
        state.has_stripe_imports = defaultValue;
    },
}

export const onboardingStore = {
    namespaced: true,
    state,
    actions,
    mutations,
}
