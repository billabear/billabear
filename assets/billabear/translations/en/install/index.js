export const INSTALL_TRANSLATIONS = {
    title: "Install",
    submit_button: "Install",
    user: {
        title: "First Admin User",
        email: "Email",
        password: "Password",
    },
    settings: {
        title: "System Settings",
        default_brand: "Default Brand Name",
        from_email: "Default From Email Address",
        timezone: "Timezone",
        webhook_url: "Base Url",
        currency: "Currency",
        country: "Country"
    },
    complete_text: "BillaBear has been installed! You can now login using the details you provided.",
    login_link: "Click here to login",
    unknown_error: "Unknown error.",
    stripe: {
        no_api_key: 'You need to provide a Stripe API key in the ENV variable STRIPE_PRIVATE_API_KEY.',
        doc_link: 'More information on how to set up BillaBear.',
        invalid_api_key: "The Stripe API key is invalid",
        support_link: "You can ask for help here."
    }
}