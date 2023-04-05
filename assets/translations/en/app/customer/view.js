export const CUSTOMER_VIEW_TRANSLATIONS = {
    title: 'View Customer Details',
    update: 'Update',
    error: {
        not_found: "No such customer found",
        unknown: "An unknown error has occurred"
    },
    main: {
        title: "Main Details",
        email: "Email",
        reference: "Internal Reference",
        external_reference: "External Reference"
    },
    address: {
        title: "Address",
        street_line_one: "Street Line 1",
        street_line_two: "Street Line 2",
        city: "City",
        region: "Region",
        post_code: "Post Code",
        country: "Country",
    },
    subscriptions: {
        title: "Subscriptions",
        list: {
            plan_name: "Plan",
            status: "Status",
            schedule: "Schedule",
            created_at: "Created At",
            valid_until: "Next Billed"
        },
        add_new: "Add New Subscription"
    },
    payment_details: {
        title: "Payment Details",
        list: {
            brand: "Brand",
            last_four: "Last Four",
            default: "Default Payment",
            expiry_month: "Expiry Month",
            expiry_year: "Expiry Year",
            name: "Name"
        },
        no_payment_details: "No payment details",
        delete: "Delete",
        make_default: "Make Default"
    }
}