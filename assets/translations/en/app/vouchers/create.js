export const VOUCHER_CREATE_TRANSLATIONS = {
    title: "Create Voucher",
    submit: "Submit",
    success_message: "Successfully, created voucher",
    fields: {
        name: "Name",
        type: "Type",
        type_percentage: "Percentage",
        type_fixed_credit: "Fixed Credit",
        percentage: "Percentage",
        entry_type: "Entry Type",
        entry_type_manual: "Manual",
        entry_type_automatic: "Automatic",
        amount: "Amount - {currency}",
        code: "Code",
        entry_event: "Event",
        event_expired_card_added: "Add new payment card during expired card warning",
    },
    help_info: {
        name: "The name of the voucher",
        type: "Percentage is a percentage off an invoice and fixed credit gives a fixed credit",
        entry_type: "Manual means the user enters a code, automatic means it's triggered by an event",
        percentage: "The percentage off",
        amount: "The amount in {currency} that the voucher provides",
        code: "The code the customer will need to provide for the voucher to be activated",
        entry_event: "The event that needs to happen for the voucher to be activated",
    }
}