export const INVOICES_CREATE_TRANSLATIONS = {
    title: "Create Invoice",
    create_invoice: "Create Invoice",
    success_message: "Invoice created",
    errors: {
        no_customer: "A customer is needed",
        nothing_to_invoice: "You need to add a subscription or a one-off item.",
        same_currency_and_schedule: "The same currency and schedule should be used for subscriptions",
        currency: "A currency is required",
        need_description: "Need a description",
        need_amount: "Need amount",
        need_tax_type: "Need a tax type"
    },
    customer: {
        create_customer: "Create Customer",
        fields: {
            customer: "Customer",
            currency: "Currency",
            due_date: "Due Date",
        },
        help_info: {
            customer: "The customer the quote is for",
            currency: "The currency to be used for the invoice",
            due_date: "The due date for the invoice, if none is given the system default is used."
        }
    },
    subscriptions: {
        title: "Subscriptions",
        add_new: "Add Subscription",
        list: {
            subscription_plan: "Subscription Plan",
            price: "Price",
            seat_number: "Seat Number"
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
            digital_product: "Digital Product",
            tax_type: "Tax Type",
        },
        tax_types: {
            digital_services: "Digital Services",
            digital_goods: "Digital Goods",
            physical: "Physical Goods/Services"
        },
    }
};