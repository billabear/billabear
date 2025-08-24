import { defineStore } from 'pinia';
import { onboardingservice } from "../services/onboardingservice";

export const useOnboardingStore = defineStore('onboarding', {
    state: () => ({
        has_stripe_imports: true,
        has_stripe_key: false,
        has_product: false,
        has_subscription_plan: false,
        has_subscription: false,
        has_customer: false,
        show_onboarding: false,
        ready: false,
        error: false,
    }),

    actions: {
        stripeImport() {
            this.markStripeImportDone();
        },

        setStripeImport({ defaultValue }) {
            this.setStripeDefaultValue(defaultValue);
        },

        async fetchData() {
            try {
                const response = await onboardingservice.fetchData();
                this.setDefaults(response.data);
                return response;
            } catch (error) {
                this.setError();
                throw error;
            }
        },

        async dismissStripeImport() {
            try {
                await onboardingservice.dismissStripeImport();
                this.markStripeImportDone();
            } catch (error) {
                console.error('Error dismissing stripe import:', error);
                this.setError();
            }
        },

        stripeKeysAdded() {
            this.markStripKeyAsDone();
        },

        productAdded() {
            this.markProductAsDone();
        },

        subscriptionPlanAdded() {
            this.markSubscriptionPlanAsDone();
        },

        subscriptionAdded() {
            this.markSubscriptionAsDone();
        },

        customerAdded() {
            this.markCustomerAsDone();
        },

        // Mutation-like methods (now just regular methods in Pinia)
        setError() {
            this.error = true;
        },

        setDefaults(defaults) {
            this.has_stripe_key = defaults.has_stripe_key;
            this.has_product = defaults.has_product;
            this.has_subscription_plan = defaults.has_subscription_plan;
            this.has_subscription = defaults.has_subscription;
            this.has_customer = defaults.has_customer;
            this.has_stripe_imports = defaults.has_stripe_imports;
            this.ready = true;

            this.show_onboarding = (
                !defaults.has_stripe_key || !defaults.has_product ||
                !defaults.has_subscription_plan || !defaults.has_stripe_imports ||
                !defaults.has_subscription || !defaults.has_customer
            );
        },

        markStripKeyAsDone() {
            this.has_stripe_key = true;
        },

        markProductAsDone() {
            this.has_product = true;
        },

        markSubscriptionAsDone() {
            this.has_subscription = true;
        },

        markCustomerAsDone() {
            this.has_customer = true;
        },

        markSubscriptionPlanAsDone() {
            this.has_subscription_plan = true;
        },

        markStripeImportDone() {
            this.has_stripe_imports = true;
        },

        setStripeDefaultValue(defaultValue) {
            this.has_stripe_imports = defaultValue;
        }
    }
});
