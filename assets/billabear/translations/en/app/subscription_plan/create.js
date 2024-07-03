export const SUBSCRIPTION_PLAN_CREATE_TRANSLATIONS = {
    title: "Create New Subscription Plan",
    main_section: {
        title: "Main Details",
        fields: {
            name: "Name",
            code_name: "Code Name",
            user_count: "User Count",
            public: "Publicly Available Plan",
            per_seat: "Per Seat",
            free: "Free",
        },
        help_info: {
            name: "The name of the subscription plan",
            code_name: "The code name for the plan to be used with the API.",

            user_count: "The number of users allowed for this plan",
            public: "Is the plan available to the public or a custom plan",
            free: "Is this a free plan?",
            per_seat: "Is the plan charged per seat?",
           }
    },
    trial_section: {
        title: "Trial Details",
        fields: {
            has_trial: "Has trial",
            is_trial_standalone: "Is Trial Standalone",
            trial_length_days: "Trial Length in Days"
        },
        help_info: {
            has_trial: "If the plan has a trial period by default",
            trial_length_days: "How long the trial should be in days",
            is_trial_standalone: "If a Trial is standalone it doesn't need a price and the subscription pauses at the end of the trial"
        }
    },
    features_section: {
        title: "Features",
        columns: {
            feature: "Feature",
            description: "Description"
        },
        create: {
            name: "Name",
            code_name: "Code Name",
            description: "Description",
            button: "Create"
        },
        add_feature: "Add",
        existing: "Existing Features",
        new: "Create New",
        no_features: "No Features"
    },
    limits_section: {
        title: "Limits",
        columns: {
            limit: "Limit",
            feature: "Feature",
            description: "Description"
        },
        fields: {
            limit: "Limit",
            feature: "Feature",
        },
        add_limit: "Add",
        no_limits: "No Limits"
    },
    prices_section: {
        title: "Prices",
        columns: {
            amount: "Amount",
            currency: "Currency",
            schedule: "Schedule"
        },
        create: {
            amount: "Amount",
            currency: "Currency",
            recurring: "Recurring",
            schedule: "Schedule",
            including_tax: "Including Tax",
            public: "Public",
            button: "Create"
        },
        add_price: "Add",
        existing: "Existing Prices",
        new: "Create New",
        no_prices: "No Prices"
    },
    submit_btn: "Create Plan"
}
