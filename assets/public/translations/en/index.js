import {CHECKOUT_TRANSLATIONS} from "./portal/checkout";

export const ENGLISH_TRANSLATIONS = {
    portal: {
        checkout: CHECKOUT_TRANSLATIONS,
        invoice: {
            pay: {
                title: "Pay",
                general: {
                    invoice_number: "Invoice Number",
                    issued_at: "Issued At",
                },
                payment: {
                    already_paid: "This invoice has already been successfully paid!",
                    amount: "The outstanding balance is: {amount} {currency}",
                    pay_button: "Pay now!"
                },
                biller_details: {
                    title: "Seller Details"
                },
                payee_details: {
                    title: "Customer Details"
                },
                lines: {
                    description: "Description",
                    tax_total: "Tax Total",
                    total: "Total",
                    tax_rate: "Tax Rate",
                },
                payment_details: {
                    title: "Payment Details"
                },
                totals: {
                    total: "Total",
                    amount_due: "Amount Due",
                },
                loading: "Loading"
            },
        },
        quote: {
            pay: {
                title: "Quote Information",
                general: {
                    invoice_number: "Invoice Number",
                    issued_at: "Issued At",
                },
                payment: {
                    already_paid: "This invoice has already been successfully paid!",
                    amount: "The outstanding balance is: {amount} {currency}",
                    pay_button: "Pay now!"
                },
                biller_details: {
                    title: "Seller Details"
                },
                payee_details: {
                    title: "Customer Details"
                },
                lines: {
                    description: "Description",
                    tax_total: "Tax Total",
                    total: "Total",
                    tax_rate: "Tax Rate",
                },
                payment_details: {
                    title: "Payment Details"
                },
                totals: {
                    total: "Total",
                    amount_due: "Amount Due",
                },
                loading: "Loading",
                not_found: "No such quote found",
                general_error: "Something unexpected happened. Try again later.",
                already_paid: "This quote has been accepted and paid for",
                has_expired: "This quote has now expired. Contact your sales rep for a new quote",
                expires_at: "This quote will expire at {date}"
            },
        },
        loading: {
            message: "Loading"
        }
    },
};