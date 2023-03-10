import axios from "axios";
import {handleResponse} from "./utils";

export const planservice = {
    fetchPlanInfo,
    createCheckout,
    createPerSeatCheckout,
    changePlan,
    cancel,
    startSubscriptionFromPaymentDetails
};


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
    })
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
    return axios.post(`/app/billing/plans/change/` + planName + '/' + paymentSchedule, {
        headers: {'Content-Type': 'application/json'},
        data: {}
    }).then(handleResponse);
}


function cancel() {
    return axios.post(`/app/billing/cancel`, {}).then(handleResponse);
}
