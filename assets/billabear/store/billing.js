import { defineStore } from 'pinia';
import { billingservice } from "../services/billingservice";

export const useBillingStore = defineStore('billing', {
    state: () => ({
        show_add_card_form: false,
        paymentDetails: [],
        error: {}
    }),

    actions: {
        addCard() {
            this.enableForm();
        },

        resetForm() {
            this.hideForm();
        },

        async fetchPaymentMethods() {
            try {
                const response = await billingservice.getPaymentDetails();
                this.setPaymentDetails(response.data.payment_details);
            } catch (error) {
                console.error('Error fetching payment methods:', error);
            }
        },

        cardAdded({ paymentDetails }) {
            this.hideForm();
            this.removeDefaultFromAllCards();
            this.addNewCard(paymentDetails);
        },

        async deleteCard({ paymentDetail }) {
            try {
                await billingservice.deletePaymentDetails(paymentDetail.id);
                this.removedCard(paymentDetail);
            } catch (error) {
                this.changeError('app.billing.payment_method.delete_error');
            }
        },

        async makeCardDefault({ paymentDetail }) {
            try {
                await billingservice.makePaymentDetailDefault(paymentDetail.id);
                this.removeDefaultFromAllCards();
                this.setCardAsDefault(paymentDetail);
            } catch (error) {
                this.changeError('app.billing.payment_method.make_default_error');
            }
        },

        // Mutation-like methods (now just regular methods in Pinia)
        changeError(error) {
            this.error = error;
        },

        enableForm() {
            this.show_add_card_form = true;
        },

        hideForm() {
            this.show_add_card_form = false;
        },

        setPaymentDetails(paymentDetails) {
            this.paymentDetails = paymentDetails;
        },

        removedCard(paymentDetail) {
            this.paymentDetails = this.paymentDetails.filter(detail => detail.id !== paymentDetail.id);
        },

        removeDefaultFromAllCards() {
            this.paymentDetails.forEach(detail => {
                detail.defaultPaymentOption = false;
            });
        },

        addNewCard(paymentDetail) {
            this.paymentDetails.push(paymentDetail);
        },

        setCardAsDefault(paymentDetail) {
            const card = this.paymentDetails.find(detail => detail.id === paymentDetail.id);
            if (card) {
                card.defaultPaymentOption = true;
            }
        }
    }
});
