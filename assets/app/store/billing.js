import {teamservice} from "../services/teamservice";
import {billingservice} from "../services/billingservice";

const state = {
    show_add_card_form: false,
    paymentDetails: [],
    error: {}
};

const actions = {
    addCard({commit}) {
        commit('enableForm')
    },
    resetForm({commit}) {
        commit('hideForm')
    },
    fetchPaymentMethods({commit}) {
        billingservice.getPaymentDetails().then(response => {
            commit('setPaymentDetails', response.data.payment_details);
        })
    },
    cardAdded({commit}, {paymentDetails}) {
        commit('hideForm');
        commit('removeDefaultFromAllCards');
        commit('addNewCard', paymentDetails);
    },
    deleteCard({commit}, {paymentDetail}) {
        billingservice.deletePaymentDetails(paymentDetail.id).then( response => {
            commit('removedCard', paymentDetail);
        }, error => {
            commit('changeError', 'app.billing.payment_method.delete_error')
        });
    },
    makeCardDefault({commit}, {paymentDetail}) {
        commit('removeDefaultFromAllCards');
    }
};

const mutations = {
    changeError(state, error) {
      state.error = error;
    },
    enableForm(state) {
      state.show_add_card_form = true;
    },
    hideForm(state) {
        state.show_add_card_form = false;
    },
    setPaymentDetails(state, paymentDetails) {
        state.paymentDetails = paymentDetails;
    },
    removedCard(state, paymentDetail) {
        var newArray = [];
        for (var i =0; i < state.paymentDetails.length; i++) {
            if (state.paymentDetails[i].id !== paymentDetail.id) {
                newArray.push(state.paymentDetails[i])
            }
        }
        state.paymentDetails = newArray;
    },
    removeDefaultFromAllCards(state) {
        for (var i =0; i < state.paymentDetails.length; i++) {
            state.paymentDetails[i].defaultPaymentOption = false;
        }
    },
    addNewCard(state, paymentDetail) {
        state.paymentDetails.push(paymentDetail)
    }
};
export const billingStore = {
    namespaced: true,
    state,
    actions,
    mutations,
}
