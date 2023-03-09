import axios from "axios";
import {handleResponse} from "./utils";

function sendAddress(address) {
    return axios.post("/api/billing/details", address).then(handleResponse);
}
function saveToken(token) {
    return axios.post("/api/billing/payment-details/token/add", {token}).then(handleResponse);
}

function getAddress() {
    return axios.get("/api/billing/details").then(handleResponse);
}

function getAddCardToken() {
    return axios.get("/api/billing/payment-details/token/start").then(handleResponse);
}

function getPaymentDetails() {
    return axios.get("/api/billing/payment-details").then(handleResponse);
}

function deletePaymentDetails(id) {
    return axios.delete("/api/billing/payment-details/"+id).then(handleResponse);
}

export const billingservice = {
    sendAddress,
    getAddress,
    getAddCardToken,
    saveToken,
    getPaymentDetails,
    deletePaymentDetails,
}
