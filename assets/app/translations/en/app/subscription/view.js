export const SUBSCRIPTION_VIEW_TRANSLATIONS = {
    title: "View Subscription",
    main: {
        title: "Subscription Data",
        status: "Status",
        plan: "Plan",
        plan_change: "Change Plan",
        customer: "Customer",
        main_external_reference: "Main External Reference",
        created_at: "Created At",
        ended_at: "Ended At",
        valid_until: "Valid Until",
        seat_number: "Seat Number",
        change_seat: "Change Seats"
    },
    pricing: {
        title: "Pricing",
        price: "Price",
        recurring: "Recurring",
        schedule: "Schedule",
        change: "Change"
    },
    payments: {
        title: "Payments",
        amount: "Amount",
        created_at: "Created At",
        view: "View",
        no_payments: "There are no payments yet"
    },
    payment_method: {
        title: "Payment Method",
        last_four: "Last Four",
        expiry_month: "Expiry Month",
        expiry_year: "Expiry Year",
        brand: 'Card Type',
        invoiced: 'Invoiced'
    },
    buttons: {
        cancel: "Cancel",
        payment_method: "Update Payment Details"
    },
    modal: {
        seats: {
            seats: "Seats",
            seats_help: "The number of seats for the plan",
            submit: "Save"
        },
        price: {
            price: "New Price",
            price_help: "The new price to be charged at next invoice",
            submit: "Update"
        },
        plan: {
            plan: "New Plan",
            plan_help: "The plan which you want to change this subscription to",
            price: "New Price",
            price_help: "The new price to be charged at next invoice",
            submit: "Update",

            when: {
                title: "When",
                next_cycle: "Use for next Billing Cycle",
                instantly: "Instantly",
                specific_date: "Specific Date",
            },
        },
        payment_method: {
            payment_method: "Use Payment Details",
            payment_method_help: "These details will be used for the next time we charge the customer.",
            update_button: "Update Payment Details",
            submit: "Update",
        },
        cancel: {
            title: "Cancel Subscription",
            cancel_btn: "Confirm",
            close_btn: "Close",
            when: {
                title: "When",
                end_of_run: "End of current billing period",
                instantly: "Instantly",
                specific_date: "Specific Date",
            },
            refund_type: {
                title: "Refund Type",
                none: "None",
                prorate: "Prorate Refund based on usage",
                full: "Full Refund"
            },
            cancelled_message: "Successfully cancelled"
        }
    }
}