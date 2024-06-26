export const SETTINGS_STRIPE_IMPORT_MAIN_TRANSLATIONS = {
    title: "Stripe Import",
    edit_config: "Edit Config",
    hide_config: "Hide Config",
    start_button: "Start Import Button",
    already_in_progress: "Import already in progress",
    list: {
        state: "State",
        last_id: "Last Id Processed",
        created_at: "Created At",
        updated_at: "Update At",
        no_results: "There have been no stripe imports so far.",
        view: "View"
    },
    danger_zone: {
        title: "Danger Zone",
        use_stripe_billing: "Use Stripe Billing to charge customers.",
        disable_billing: "Disable Stripe Billing",
        enable_billing: "Enable Stripe Billing",
    },
    disable_billing_modal: {
        title: "Disable Stripe Billing",
        disable_all_subscriptions: "By disabling Stripe Billing, you are saying you no longer want Stripe to manage charge customers but for BillaBear to manage this. This will save you money.",
        warning: "Once disabled, if you wish to go back to using Stripe Billing you will need to manually resubscribe everyone.",
        cancel: "Cancel",
        confirm: "Confirm"
    },
    webhook: {
        title: "Webhook",
        url: "Webhook URL",
        register_webhook: "Register Webhook",
        deregister_webhook: "Deregister Webhook",
        help_info: {
            url: "A https URL that is publically available for webhook calls."
        }
    },
    stripe_config: {
        title: "Stripe API Keys",
        description: "In order to use Stripe you need to configure the API keys.",
        stripe_private_key: "Private Key",
        help_info: {
            stripe_private_key: "The API key to be used to authenticate backend requests",
            stripe_public_key: "The API key to be used to authenticate frontend requests.",
        },
        stripe_public_key: "Public Key",
        submit_button: "Submit",
        error: "Unable to confirm Stripe API Keys.",
    }
}
