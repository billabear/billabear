export const PRICE_CREATE_TRANSLATIONS = {
    title: "Create New Price",
    amount: "Amount",
    external_reference: "External Reference",
    advance: "advance",
    submit_btn: "Create Price",
    show_advanced: "Advanced",
    success_message: "Successfully created price",
    schedule_label: "Payment Schedule",
    currency: "Currency",
    recurring: "Is A Recurring?",
    including_tax: "Does Price includes tax?",
    public: "Public",
    help_info: {
        amount: "The price is the minor level currency. So 1.00 USD would be 100 and 9.99 would be 999.",
        display_amount: "This price would be {amount}.",
        external_reference: "The reference for the product that is used by the payment provider. Leave empty unless you're extremely confident you have the correct reference.",
        recurring: "If this is recurring payment or one-off.",
        currency: "The currency that the customer should be charged in",
        schedule: "How often the customer should be charged",
        including_tax: "If you want to hide the tax within the price or if you want to make the customer pay the tax themselves",
        public: "If this is a publicly displayable price"
    },
    schedule: {
        week: "Weekly",
        month: "Monthly",
        year: "Yearly"
    }
}