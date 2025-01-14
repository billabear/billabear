export const STATE_EDIT_TRANSLATIONS =  {
    title: "Edit State",
    state: {
        fields: {
            name: "Name",
            code: "Code",
            collecting: "Collecting",
            threshold: "Threshold",
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
            name: "The name of the state",
            code: "The code that is often used as a shorthand for the state",
            collecting: "If we're always collecting tax for the state",
            threshold: "What the economic threshold for the state",
            transaction_threshold: "What the transaction threshold for the state",
            threshold_type: "How the time period for the threshold calculation is determined"
        }
    },
    update_button: "Update"
}
