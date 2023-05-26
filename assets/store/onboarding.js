const state = {
    has_stripe_imports: true,
}

const actions = {
    stripeImport({commit}) {
        commit('markStripeImportDone');
    },
    setStripeImport({commit}, {defaultValue}) {
        commit('setStripeDefaultValue', defaultValue)
    }
}

const mutations = {
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