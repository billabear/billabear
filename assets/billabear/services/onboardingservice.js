import axios from "axios";

/**
 * Service for handling onboarding-related API operations
 */
export const onboardingservice = {
    /**
     * Fetch system data for onboarding
     * @returns {Promise} - Promise that resolves with system data
     */
    fetchData() {
        return axios.get("/app/system/data");
    },

    /**
     * Dismiss stripe import notification
     * @returns {Promise} - Promise that resolves when dismissed
     */
    dismissStripeImport() {
        return axios.post("/app/settings/stripe-import/dismiss");
    }
};