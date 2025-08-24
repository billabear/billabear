import axios from "axios";
import {handleResponse} from "./utils";

function fetchPlanInfo() {
    return axios.get(`/app/billing/plans`, {
        headers: {'Content-Type': 'application/json'},
        data: {}
    }).then(handleResponse);
}

function startSubscriptionFromPaymentDetails(planName, paymentSchedule, currency, numberOfSeats = 1) {
    return axios.post('/app/billing/subscription/start', {
        plan_name: planName,
        schedule: paymentSchedule,
        currency: currency,
        seat_numbers: numberOfSeats,
    }).then(handleResponse);
}

function createCheckout(planName, paymentSchedule, currency) {
    return axios.post(`/app/billing/plans/checkout/` + planName + '/' + paymentSchedule + '/' + currency, {}, {
        headers: {'Content-Type': 'application/json'},
    }).then(handleResponse);
}

function createPerSeatCheckout(planName, paymentSchedule, currency, seats) {
    return axios.post(`/app/billing/plans/checkout/` + planName + '/' + paymentSchedule + '/' + currency, {seats})
        .then(handleResponse);
}

function changePlan(planName, paymentSchedule) {
    return axios.post(`/app/billing/plans/change/` + planName + '/' + paymentSchedule, {}, {
        headers: {'Content-Type': 'application/json'}
    }).then(handleResponse);
}


function cancel() {
    return axios.post(`/app/billing/cancel`, {}).then(handleResponse);
}

// Plan management functions for admin/store operations
function fetchSubscriptionPlan(productId, subscriptionPlanId) {
    return axios.get(`/app/product/${productId}/plan/${subscriptionPlanId}/update`);
}

function fetchPlanCreationData(productId) {
    return axios.get(`/app/product/${productId}/plan-creation`);
}

function createFeature(feature) {
    return axios.post('/app/feature', feature);
}

function createPrice(productId, price) {
    return axios.post(`/app/product/${productId}/price`, price);
}

export const planservice = {
    fetchPlanInfo,
    createCheckout,
    createPerSeatCheckout,
    changePlan,
    cancel,
    startSubscriptionFromPaymentDetails,
    // Plan management methods
    fetchSubscriptionPlan,
    fetchPlanCreationData,
    createFeature,
    createPrice
};
