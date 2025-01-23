export const COUNTRY_EDIT_TRANSLATIONS = {
    title: "Edit Country",
    country: {
        fields: {
            name: "Name",
            iso_code: "Country Code",
            currency: "Currency",
            threshold: "Threshold",
            in_eu: "In the EU?",
            tax_year: "Start of Tax Year",
            enabled: "Enabled",
            collecting: "Collect Tax",
            tax_number: "Tax Number",
            transaction_threshold: "Transaction Threshold",
            threshold_type: "Threshold Type",
            threshold_types: {
                rolling: "Rolling Yearly",
                calendar: "Calendar Year",
                rolling_quarterly: "Rolling by quarters",
                rolling_accounting: "Rolling by accounting year",
            }
        },
        help_info: {
            name: "The name of the country",
            iso_code: "The ISO code for the country",
            currency: "The reporting currency for the country",
            threshold: "The tax threshold for the country",
            in_eu: "Is the country within the EU",
            tax_year: "The date for the start of the tax year for the country",
            enabled: "If the country is enabled for customer sign ups",
            collecting: "If tax should always be collected for this country",
            tax_number: "Your tax number for this country.",
            transaction_threshold: "What the transaction threshold for the state",
            threshold_type: "How the time period for the threshold calculation is determined"
        }
    },
    update_button: "Update"
}
