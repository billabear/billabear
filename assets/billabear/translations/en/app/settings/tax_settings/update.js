export const TAX_SETTINGS_UPDATE_TRANSLATION = {
    title: "Tax Settings",
    submit_btn: "Submit",
    success_message: "Updated tax settings",
    fields: {
        tax_customers_with_tax_number: "Tax Customers With Tax Number",
        eu_business_tax_rules: "Handle EU Business tax rules",
        eu_one_stop_shop_rule: "EU One Stop Shop Rule",
    },
    help_info: {
        tax_customers_with_tax_number: "If not checked then customers who have provided a tax number isn't charged tax",
        eu_business_tax_rules: "If enabled then business customers who have provided a VAT number will be handled differently from normal customers",
        eu_one_stop_shop_rule: "Apply the EU one stop shop rule. Where EU countries are taxed regardless of threshold.",
    }
}
