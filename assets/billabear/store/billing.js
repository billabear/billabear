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
        state.paymentDetails = state.paymentDetails.filter(detail => detail.id !== paymentDetail.id);
    },
    removeDefaultFromAllCards(state) {
        state.paymentDetails.forEach(detail => {
            detail.defaultPaymentOption = false;
        });
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
