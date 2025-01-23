export const METRIC_CREATE_TRANSLATIONS = {
    title: "Create Metric",
    fields: {
        name: "Name",
        code: "Code",
        type: "Type",
        aggregation_method: "Aggregation Method",
        aggregation_property: "Aggregation Property",
        ingestion: "Ingestion",
        filters: "Filters"
    },
    help_info: {
        name: "The name of the metric",
        code: "The code that is to be used in api calls. Lower case letters, numbers and underscore only.",
        type: "If the counter for the customer is to be reset at the end of a subscription period",
        aggregation_method: "How the events that are sent to BillaBear are to be aggregated.",
        aggregation_property: "Which property in the event data is to be used for aggregation.",
        ingestion: "How often events should be processed",
        filters: "The filters that should be applied to the event payload to be excluded in aggregation"
    },
    aggregation_methods: {
        count: "Count",
        sum: "Sum",
        latest: "Latest",
        unique_count: "Unique Count",
        max: "Max"
    },
    ingestion: {
        real_time: "Real Time",
        hourly: "Hourly",
        daily: "Daily"
    },
    filter: {
        name: "Name",
        value: "Value",
        type: "Type",
        no_filters: "No Filters"
    },
    filter_type: {
        inclusive: "Inclusive",
        exclusive: "Exclusive"
    },
    create_button: "Create"
}
