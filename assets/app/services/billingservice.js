import axios from "axios";
import {handleResponse} from "./utils";

function sendAddress(address) {
    return axios.post("/app/billing/details", address).then(handleResponse);
}
function saveToken(customerId, token) {
    return axios.post("/app/customer/"+customerId+"/payment-card/frontend-payment-token", {token}).then(handleResponse);
}
function portalPay(invoiceId, token) {
    return axios.post("/public/invoice/"+invoiceId+"/pay", {token}).then(handleResponse);
}
function portalQuotePay(quoteId, token) {
    return axios.post("/public/quote/"+quoteId+"/pay", {token}).then(handleResponse);
}
function portalCheckoutPay(slug, checkout_session, token) {
    return axios.post("/public/checkout/"+slug+"/pay", {checkout_session, token}).then(handleResponse);
}

function getAddress() {
    return axios.get("/app/billing/details").then(handleResponse);
}

function getAddCardToken(customerId) {
    return axios.get("/app/customer/"+customerId+"/payment-card/frontend-payment-token").then(handleResponse);
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
    portalPay,
    portalQuotePay,
    portalCheckoutPay,
}
