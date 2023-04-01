import axios from "axios";
import {handleResponse} from "./utils";

function sendAddress(address) {
    return axios.post("/app/billing/details", address).then(handleResponse);
}
function saveToken(customerId, token) {
    return axios.post("/app/customer/"+customerId+"/payment-details/frontend-payment-token", {token}).then(handleResponse);
}

function getAddress() {
    return axios.get("/app/billing/details").then(handleResponse);
}

function getAddCardToken(customerId) {
    return axios.get("/app/customer/"+customerId+"/payment-details/frontend-payment-token").then(handleResponse);
}

function getPaymentDetails() {
    return axios.get("/app/billing/payment-details").then(handleResponse);
}

function deletePaymentDetails(id) {
    return axios.delete("/app/billing/payment-details/"+id).then(handleResponse);
}

export const billingservice = {
    sendAddress,
    getAddress,
    getAddCardToken,
    saveToken,
    getPaymentDetails,
    deletePaymentDetails,
}
