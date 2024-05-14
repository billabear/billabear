export const EMAIL_TEMPLATES_UPDATE_TRANSLATIONS = {
    title: "Update Email Template",
    fields: {
        name: "Name",
        locale: "Locale",
        use_emsp_template: "Use EMSP Template",
        subject: "Subject",
        template_body: "Template Body",
        template_id: "Template ID"
    },
    help_info: {
        name: "Which email is this template for",
        locale: "Which locale is this template for.",
        use_emsp_template: "If the template system for the email service provider you're using should be used. If unsure leave unchecked",
        subject: "The message to be put in the subject",
        template_body: "The TWIG template that is to be used to generate the html for the email.",
        template_id: "The template ID given to you by your email service provider where you created the template. If unsure uncheck use emsp template.",
        variable_docs: "Check the documentation to see what variables are available",
    },
    submit_btn: "Update",
    success_message: "Successfully updated email template"
}