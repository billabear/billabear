export const SYSTEM_SETTINGS_UPDATE_TRANSLATIONS = {
    title: "System Settings",
    submit_btn: "Update",
    success_message: "Updated system settings",
    fields: {
        system_url: "System URL",
        timezone: "Timezone",
        invoice_number_generation: "Invoice Number Generation",
        subsequential_number: 'Subsequential Number',
        default_invoice_due_time: "Default Invoice Due Time",
        format: "Format",
        invoice_generation: "Invoice Generation"
    },
    help_info: {
        system_url: "The base url that BillaBear can be found at.",
        timezone: "The default timezone for the system",
        invoice_number_generation: "How the invoice number is generated. Random is a random string and subsequent means it's a number that increments",
        subsequential_number: "The last invoice number used. The next invoice number will be one digit higher",
        default_invoice_due_time: "How long between invoice creation and the due date",
        format: "The format that is to be used for invoice number generation. %S is the subsequential number and %R for 8 random characters.",
        invoice_generation: "When new invoice for subscriptions are to be generated",
    },
    invoice_generation_types: {
        periodically: "Periodically",
        end_of_month: "End of Month",
    },
    invoice_number_generation: {
        random: "Random Number",
        subsequential: "Subsequential",
        format: "Format",
    },
    default_invoice_due_time: {
        '30_days': '30 Days',
        '60_days': '60 Days',
        '90_days': '90 Days',
        '120_days': '120 Days',
    }
};
