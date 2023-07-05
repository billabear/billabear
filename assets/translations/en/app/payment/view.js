export const PAYMENT_VIEW_TRANSLATION = {
    title: "Payment Details",
    main: {
        title: "Main Details",
        amount: "Amount",
        currency: "Currency",
        external_reference: "External Reference",
        status: "Status",
        created_at: "Created At"
    },
    customer: {
        title: "Customer",
        email: "Email",
        more_info: "More Info",
        country: "Country",
        attach: "Attach to customer"
    },
    refunds: {
        title: "Refunds",
        amount: "Amount",
        reason: "Reason",
        created_by: "Created By",
        created_at: "Created At",
        none: "No refunds found"
    },
    subscriptions: {
        title: "Subscriptions",
        plan_name: "Plan Name",
        more_info: "More Info",
        none: "Payment not linked to subscriptions"
    },
    receipts: {
        title: "Receipts",
        created_at: "Created At",
        download: "Download",
        none: "Payment has no receipts"
    },
    buttons: {
        refund: "Issue Refund",
        generate_receipt: "Generate Receipt"
    },
    modal: {
        attach: {
            title: "Attach To Customer",
            button: 'Attach'
        },
        refund: {
            title: "Refund",
            amount: {
                title: "Amount",
                help_info: "This is the minor currency amount. So 100 USD is 1.00 USD."
            },
            reason: {
                title: "Reason"
            },
            submit: "Issue Refund",
            success_message: "Refund successfully created",
            error_message: "Something went wrong",
        }
    }
}