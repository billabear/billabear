import { defineStore } from 'pinia';
import { planservice } from "../services/planservice";

export const usePlanStore = defineStore('plan', {
    state: () => ({
        prices: [],
        features: [],
        loaded: false,
        sendingRequest: false,
        selectedFeatures: [],
        selectedLimits: [],
        selectedPrices: [],
        errors: {},
        metrics: [],
    }),

    actions: {
        async fetchSubscriptionPlan({ productId, subscriptionPlanId }) {
            try {
                const response = await planservice.fetchSubscriptionPlan(productId, subscriptionPlanId);
                this.setSubscriptionPlanData(response.data);
                return response;
            } catch (error) {
                throw error;
            }
        },

        async fetchData({ productId }) {
            try {
                const response = await planservice.fetchPlanCreationData(productId);
                this.setData(response.data);
                return response;
            } catch (error) {
                throw error;
            }
        },

        addFeatureToSelected({ feature }) {
            this.addFeature(feature);
        },

        removeFeatureFromSelected({ key }) {
            this.removeFeature(key);
        },

        addLimitToSelected({ limit }) {
            this.addLimit(limit);
        },

        removeLimitFromSelected({ key }) {
            this.removeLimit(key);
        },

        addPriceToSelected({ price }) {
            this.addPrice(price);
        },

        removePriceFromSelected({ key }) {
            this.removePrice(key);
        },

        reset() {
            this.resetEverything();
        },

        async createFeature({ feature }) {
            this.markAsSendingRequest();
            try {
                const response = await planservice.createFeature(feature);
                this.addNewFeature(response.data);
                return response;
            } catch (error) {
                if (error.response !== undefined) {
                    this.setErrors(error.response.data.errors);
                }
                this.markAsNotSendingRequest();
                throw error;
            }
        },

        async createPrice({ productId, price }) {
            this.markAsSendingRequest();
            try {
                const response = await planservice.createPrice(productId, price);
                this.addNewPrice(response.data);
                return response;
            } catch (error) {
                if (error.response !== undefined) {
                    this.setErrors(error.response.data.errors);
                }
                this.markAsNotSendingRequest();
                throw error;
            }
        },

        // Mutation-like methods (now just regular methods in Pinia)
        markAsSendingRequest() {
            this.errors = {};
            this.sendingRequest = true;
        },

        markAsNotSendingRequest() {
            this.sendingRequest = false;
        },

        setErrors(errors) {
            this.errors = errors;
            this.sendingRequest = false;
        },

        setData(payload) {
            this.features = payload.features;
            this.prices = payload.prices;
            this.metrics = payload.metrics;
            this.loaded = true;
        },

        setSubscriptionPlanData(payload) {
            this.selectedPrices = payload.subscription_plan.prices;
            this.selectedLimits = payload.subscription_plan.limits;
            this.selectedFeatures = payload.subscription_plan.features;
            this.features = payload.features;
            this.prices = payload.prices;
            this.metrics = payload.metrics;
            this.loaded = true;
        },

        addNewFeature(feature) {
            this.features.push(feature);
            this.selectedFeatures.push(feature);
            this.sendingRequest = false;
        },

        addFeature(feature) {
            this.selectedFeatures.push(feature);
        },

        removeFeature(key) {
            this.selectedFeatures.splice(key, 1);
        },

        addLimit(limit) {
            this.selectedLimits.push(limit);
        },

        removeLimit(key) {
            this.selectedLimits.splice(key, 1);
        },

        addNewPrice(price) {
            this.prices.push(price);
            this.selectedPrices.push(price);
            this.sendingRequest = false;
        },

        addPrice(price) {
            this.selectedPrices.push(price);
        },

        removePrice(key) {
            this.selectedPrices.splice(key, 1);
        },

        resetEverything() {
            this.selectedPrices = [];
            this.selectedFeatures = [];
            this.selectedLimits = [];
        }
    }
});
