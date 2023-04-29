export const NOTIFICATION_SETTINGS_UPDATE_TRANSLATIONS = {
    title: "Notification Settings",
    submit_btn: "Update",
    success_message: "Updated notification settings",
    fields: {
        send_customer_notifications: "Send Customer Notifications",
        emsp: "Email Service Provider",
        emsp_api_key: "Email Service Provider - API Key",
        emsp_api_url: "Email Service Provider - API URL",
        emsp_domain: "Email Service Provider - Domain",
        default_outgoing_email: "Default Outgoing Email",
    },
    help_info: {
        emsp: "Which email provider you want to use. If not sure use system.",
        emsp_api_key: "The API key provided by the email service provider.",
        emsp_api_url: "The API URL provided by the email service provider.",
        emsp_domain: "The domain by the email service provider.",
        send_customer_notifications: "If you want BillaBear to send notifications to customers such as subscription creation, paused, payment receipt, etc.",
        default_outgoing_email: "The default email address to be used for sending notifications when no brand settings exist",
    }
};