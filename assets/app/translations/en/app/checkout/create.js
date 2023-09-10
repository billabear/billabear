export const CHECKOUT_CREATE_TRANSLATIONS = {
    title: "Create Checkout",
    create_quote: "Create Checkout",
    success_message: "Checkout created",
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
            name: "Name",
            permanent: "Permanent",
            customer: "Customer",
            currency: "Currency",
            slug: "Slug",
            expires_at: "Expires At",
            brand: "Brand"
        },
        help_info: {
            permanent: "If the checkout is permanent or a one-time checkout",
            name: "The identifying name for the checkout",
            customer: "The customer the checkout is for",
            currency: "The currency to be used for the checkout",
            expires_at: "When the quote expires and can't be paid for",
            slug: "The slug for the URL. If you want the checkout to have a pretty url use this.",
            brand: "The brand the checkout out belongs"
        }
    },
    subscriptions: {
        title: "Subscriptions",
        add_new: "Add Subscription",
        list: {
            subscription_plan: "Subscription Plan",
            price: "Price",
            per_seat: "Per Seat"
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