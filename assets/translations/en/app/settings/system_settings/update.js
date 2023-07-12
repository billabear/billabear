export const SYSTEM_SETTINGS_UPDATE_TRANSLATIONS = {
    title: "System Settings",
    submit_btn: "Update",
    success_message: "Updated system settings",
    fields: {
        system_url: "System URL",
        timezone: "Timezone",
        invoice_number_generation: "Invoice Number Generation",
        subsequential_number: 'Subsequential Number'
    },
    help_info: {
        system_url: "The base url that billabear can be found at.",
        timezone: "The default timezone for the system",
        invoice_number_generation: "How the invoice number is generated. Random is a random string and subsequent means it's a number that increments",
        subsequential_number: "The last invoice number used. The next invoice number will be one digit higher",
    },
    invoice_number_generation: {
        random: "Random Number",
        subsequential: "Subsequential"
    }
};