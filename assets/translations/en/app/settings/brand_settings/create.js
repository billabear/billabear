export const BRAND_SETTINGS_CREATE_TRANSLATIONS = {
    title: "Create Brand Settings",
    fields: {
        name: "Name",
        email: "Email Address",
        company_name: "Company Name",
        street_line_one: "Street Line 1",
        street_line_two: "Street Line 2",
        city: "City",
        region: "Region",
        country: "Country",
        post_code: "Post Code",
        code: "Code",
        tax_number: "Tax Number",
        tax_rate: "Tax Rate"
    },
    help_info: {
        name: "The name of the brand",
        code: "The code to be used to identify the brand in API calls. This can't be updated. Most be lower alphanumeric case with underscores only.",
        tax_number: "The tax number for the brand/company",
        email: "The email to be used when sending emails to brand customer",
        company_name: "The company name for being billing purposes",
        street_line_one: "The first line of the street billing address",
        street_line_two: "The second line of the street billing address",
        city: "The city for the billing address",
        region: "The region/state for the billing address",
        country: "The customer's billing country - ISO 3166-1 alpha-2 country code.",
        postcode: "The post code for the billing address",
        tax_rate: "The rax rate that is to be used for your home country or when no other tax rate can be found"
    },
    address_title: "Billing Address",
    success_message: "Updated",
    submit_btn: "Create",
};