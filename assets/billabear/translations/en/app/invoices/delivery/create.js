export const INVOICE_DELIVERY_CREATE = {
    title: "Create New Invoice Delivery",
    fields: {
        method: "Method",
        format: "Format",
        sftp: {
            port: "Port",
            hostname: "Hostname",
            directory: "Directory",
            username: "Username",
            password: "Password"
        },
        webhook: {
            method: "Method",
            url: "URL"
        },
        email: {
            email: "Email",
            help_info: "If no email is provided the customer's email will be used by default"
        }
    },
    save: "Save"
}
