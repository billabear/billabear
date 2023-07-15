export const QUOTES_CREATE_TRANSLATIONS = {
    title: "Create Quote",
    create_quote: "Create Quote",
    create_invoice: "Create Invoice",
    success_message: "Invoice created",
    errors: {
        no_customer: "A customer is needed",
        nothing_to_invoice: "You need to add a subscription or a one-off item.",
        same_currency_and_schedule: "The same currency and schedule should be used for subscriptions",
        currency: "A currency is required",
        need_description: "Need a description",
        need_amount: "Need amount"
    },
    customer: {
        create_customer: "Create Customer",
        fields: {
            customer: "Customer",
            currency: "Currency"
        },
        help_info: {
            customer: "The customer the quote is for",
            currency: "The currency to be used for the invoice",
        }
    },
    subscriptions: {
        title: "Subscriptions",
        add_new: "Add Subscription",
        list: {
            subscription_plan: "Subscription Plan",
            price: "Price"
        },
        no_subscriptions: "No Subscriptions",
        add_subscription: "Add Subscription"
    },
    items: {
        title: "One-off Items",
        add_item: "Add One-off Item",
        no_items: "No one-off items",
        list: {
            description: "Description",
            amount: "Amount",
            tax_included: "Tax Included",
            digital_product: "Digital Product"
        }
    }
};