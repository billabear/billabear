import axios from "axios";

const state = {
    prices: [],
    features: [],
    loaded: false,
    sendingRequest: false,
    selectedFeatures: [],
    selectedLimits: [],
    selectedPrices: [],
    errors: {}
}

const actions = {
    fetchSubscriptionPlan({commit}, {productId, subscriptionPlanId}) {
        return new Promise((resolve, reject) => {
            axios.get('/app/product/'+productId+'/plan/'+subscriptionPlanId+'/update').then(response => {
                commit('setSubscriptionPlanData', response.data);
                resolve(response);
            }).catch(error => {
                reject(error)
            })
        })
    },
    fetchData({commit}, {productId}) {
        return new Promise((resolve, reject) => {
            axios.get('/app/product/'+productId+'/plan-creation').then(response => {
                commit('setData', response.data);
                resolve(response);
            }).catch(error => {
                reject(error)
            })
        })
    },
    addFeatureToSelected({commit}, {feature}) {
        commit('addFeature', feature);
    },
    removeFeatureFromSelected({commit}, {key}) {
        commit('removeFeature', key);
    },
    addLimitToSelected({commit}, {limit}) {
        commit('addLimit', limit);
    },
    removeLimitFromSelected({commit}, {key}) {
        commit('removeLimit', key);
    },
    addPriceToSelected({commit}, {price}) {
        commit('addPrice', price)
    },
    removePriceFromSelected({commit}, {key}) {
        commit('removePrice', key)
    },
    createFeature({commit}, {feature}) {
        commit('markAsSendingRequest')
        return new Promise((resolve, reject) => {
            axios.post('/app/feature', feature).then(
                response => {
                    commit('addNewFeature', response.data)
                    resolve(response)
                }
            ).catch(error => {
                if (error.response !== undefined) {
                    commit('setErrors', error.response.data.errors)
                }
                commit('markAsNotSendingRequest')
                reject(error)
            })
        });
    },
    createPrice({commit}, {productId, price}) {
        commit('markAsSendingRequest')
        return new Promise((resolve, reject) => {
            axios.post('/app/product/' + productId + '/price', price).then(
                response => {
                    commit('addNewPrice', response.data);
                    resolve(response);
                }
            ).catch(error => {
                if (error.response !== undefined) {
                    commit('setErrors', error.response.data.errors)
                }
                commit('markAsNotSendingRequest')
                reject(error)
            })
        });
    }
}

const mutations = {
    markAsSendingRequest(state) {
        state.errors = {};
        state.sendingRequest = true;
    },
    markAsNotSendingRequest(state) {
        state.sendingRequest = false;
    },
    setErrors(state, errors) {
        state.errors = errors;
        state.sendingRequest = false;
    },
    setData(state, payload) {
        state.features = payload.features;
        state.prices = payload.prices;
        state.loaded = true;
    },
    setSubscriptionPlanData(state, payload) {
        state.selectedPrices = payload.subscription_plan.prices;
        state.selectedLimits = payload.subscription_plan.limits;
        state.selectedFeatures = payload.subscription_plan.features;
        state.features = payload.features;
        state.prices = payload.prices;
        state.loaded = true;
    },
    addNewFeature(state, feature) {
        state.features.push(feature)
        state.selectedFeatures.push(feature)
        state.sendingRequest = false;
    },
    addFeature(state, feature) {
        state.selectedFeatures.push(feature)
    },
    removeFeature(state, key) {
        state.selectedFeatures.splice(key, 1);
    },
    addLimit(state, feature) {
        state.selectedLimits.push(feature)
    },
    removeLimit(state, key) {
        state.selectedLimits.splice(key, 1);
    },
    addNewPrice(state, price) {
        state.prices.push(price)
        state.selectedPrices.push(price)
        state.sendingRequest = false;
    },
    addPrice(state, price) {
        state.selectedPrices.push(price)
    },
    removePrice(state, key){
        state.selectedPrices.splice(key, 1);
    }
}

export const planStore = {
    namespaced: true,
    state,
    actions,
    mutations,
}
