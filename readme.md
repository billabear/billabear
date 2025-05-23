<p align="center">
  <img width="450px" src="https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo-v2.png">
</p>

<p align="center">
  <h1 style="text-align: center">BillaBear - The Best Self-Hostable Billing System</h1>
</p>

BillaBear is a standalone Subscription Management and Billing System that integrates with Stripe. It provides a REST API
allowing you to integrate it easily.

You can get the managed hosted version at https://www.billabear.com. The managed cloud hosted version gets updates on a regular basis while the open version GitHub gets a new feature release every 6 months.

## Documentation

* [User Documentation](https://docs.billabear.com/user/) - How to use BillaBear as a user
* [Technical Documentation](https://docs.billabear.com/technical/) - Technical information for hosting and integration
* [Swagger](https://swagger.billabear.com) - The REST API docs for integration

## Features

More features than you can shake a stick at, including:

* Tax System (Thresholds, Multiple Tax Types, Country Tax Rules, State Tax Rules)
* Workflow system
* Plan Management
* Subscription Management
* Slack Integration
* Invoice System
* Hosted Checkout
* Paylinks
* Quotes
* Mass Subscription Change System
* Reports (Subscription, Lifetime Value, Churn)
* Email Service Provider (EMSP) API integration (SendGrid, Mailgun, PostMark)
* Email Template Management - Either via EMSP templates or twig templates
* Document Management (Invoice, PDF, Quote) - Via Twig templates
* Multiple Brand support
* Multiple Currency Support
* Multiple Language Support
* Subscription Add-ons
* Webhooks
* Dunning
* Vouchers
* Credit notes
* And more

### Workflows

Workflows are a powerful feature of BillaBear. They allow you to integrate important tasks that have to be done during a process that if it fails you want to be able to retry later and resume with the rest of the process. This avoids manual developer intervention which can be timely and annoying.

For example, you might want a microservice to be called on a subscription creation to allow for the creation of resources.

Another example of where workflows are useful is when you're issuing refunds and for some reason the connection/API rquest fails. You can retry the refund later without requiring manual intervention.

### Templates

BillaBear uses Twig templates for the generation of documents such as invoices, quotes, and emails. This allows you to customize the look and feel of these documents to match your brands. It also allows for using the email service provider's templates. Both of these allows other departments other than tech to make minor changes, no more minor tickets to change the wording in an invoice or an email.

### Tax Management

BillaBear has a powerful tax system that allows you to set up tax rules for countries and states. This allows you to configure your tax system to match the legal requirements for your specific product type.

EU Tax laws such as reverse charge and one-stop-shop are supported. And there is an integration with VAT Sense so you can automate the syncing of tax rules for the EU and countries other than US and Canada. 

The system is aware of thresholds which are configurable per country and state. This allows you to set up the system to charge tax only when you reach a certain threshold. And you can configure it to receive notifications when a threshold is reached.

### Pricing

BillaBear allows you to have the pricing you want. 

Pricing Examples:

* A fixed price for a plan.
* Sell packages, say 200,000 euros of revenue. Which can also be sold in usage so they are billed based on their previous month's usage.
* Have tier volume pricing where the price per unit decreases as the volume increases.
* Have stair pricing (called tier graduated) where the price per unit decreases as the volume increases but only after a certain volume.
* Have tiered volume with a fixed fee and per unit fee.
* Seats pricing where you charge per seat.

### Customer Facing Frontend

BillaBear allows you to use Stripe.JS by providing you a token that is registered with Stripe to be used with the customer. This allows you all the normal flexibility that Stripe.JS allows for.

### Integrations

* DocRaptor - PDF Generation
* SendGrid - Email Service Provider
* Mailgun - Email Service Provider
* PostMark - Email Service Provider
* Stripe - Payment Provider
* VAT Sense - Tax Rules
* Slack - Notifications
* Xero - Accounting
* EasyBill - Accounting
* Mailchimp - Marketing
* EmailOctopus - Marketing
* FreshDesk - Help Desk
* Zendesk - Help Desk

### Feature Comparison Matrix

| Feature                           | BillaBear | Lago Open Source | Lago Cloud | Stripe | ChargeBee | Recurly | KillBill |
|-----------------------------------|-----------|------------------|------------|--------|-----------|---------|----------|
| Usage Billing                     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| Tiered Pricing                    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| Stair Pricing                     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| Package Pricing                   | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| One off Charges                   | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| **Payment Providers**             |           |                  |            |        |           |         |          |
| Stripe                            | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| Adyen                             | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| PayPal                            | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| GoCardless                        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Invoice                           | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| **Finance**                       |           |                  |            |        |           |         |          |
| Global Tax Support                | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Xero Integration                  | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| EasyBill Integration              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| FreshBooks Integration            | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Netsuite Integration              | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Pipe Integration                  | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Quickbooks Integration            | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Sage Intacct Integration          | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| VatSense Integration              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Anrok Integration                 | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| **Help Desk**                     |           |                  |            |        |           |         |          |
| FreshDesk                         | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Grove                             | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Zendesk                           | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| **Communications**                |           |                  |            |        |           |         |          |
| Transactional Emails              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| Full Control Over Email Templates | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Full Controll Over PDF Templates  | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Choose PDF generation engine      | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Mailgun Integration               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| SendGrid Integration              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Postmark Integration              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| SMS Notifications                 | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Slack Integration                 | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20"> |
| Zapier Integration                | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| EmailOctopus Integration          | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Mailchimp Integration             | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| **User Self-Service**             |           |                  |            |        |           |         |          |
| Hosted Checkout Page              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Hosted Paylink Page               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| Hosted Details Manage             | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">               | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">    | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |
| **Technical**                     |           |                  |            |        |           |         |          |
| Self-Hostable                     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| Extendable                        | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">         | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">      |
| Slack Support                     | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">       | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">              | <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Eo_circle_green_checkmark.svg" alt="Yes" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">     | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">        | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">      | <img src="https://upload.wikimedia.org/wikipedia/commons/c/cc/Cross_red_circle.svg" alt="No" width="20">       |

## Benefits for Developers

* The ability to extend the platform to meet your needs.
* A workflow system that allows you to add webhook events into your workflow to ensure that crucial events are handled.
* The ability to define your own templates for emails and PDFs via Twig.
* The ability for others to make easy changes to templates instead of it being a developer task
* The ability to define your own tax rules and rates which can make tax changes less painful.
* The ability to choose which PDF generation engine you want to use.
* And a lot more

## How to Integrate

To start integrating with BillaBear you can use the REST API.

* [Swagger Docs](https://swagger.billabear.com)
* [Technical Documentation For API](https://docs.billabear.com/technical/)
* [User Documentation](https://docs.billabear.com/user/)

### SDKS

* [PHP](https://github.com/billabear/php-sdk)
* [Java](https://github.com/billabear/java-sdk)
* [Go](https://github.com/billabear/go-sdk)
* [JavaScript](https://github.com/billabear/javascript-sdk)
* [Ruby](https://github.com/billabear/ruby-sdk)
* [Python](https://github.com/billabear/python-sdk)

## Getting Started

To get started using this repository you can get up and running using Docker and Docker compose.

### Host on DigitalOcean

You can deploy to DigitalOcean with just a click of the button below and it'll deploy using the DigitalOcean App
Platform.

[![Deploy to DO](https://www.deploytodo.com/do-btn-blue.svg)](https://cloud.digitalocean.com/apps/new?repo=https://github.com/billabear/billabear/tree/main)

### Docker Compose

BillaBear is deployable using docker-compose using the docker-compose.yaml found
in https://github.com/billabear/hosting-docker-compose.

```
git clone git@github.com:billabear/hosting-docker-compose.git
cd hosting-docker-compose
docker compose up -d
```

### Managed Cloud Hosting

You can get managed cloud hosting at https://billabear.com. 

The managed cloud hosted version gets updates on a regular basis while the open version GitHub gets a new feature release every 6 months.

### Development

```
docker compose up -d
```

Then go to http://localhost and follow the install instructions.

## FAQ

### Is BillaBear Open Source?

It's available free to use under the Fair Core License that adds restrictions on competing for 2-years.

There are arguments about if these licenses are open source or not, but generally for most we care can we use it for
free and can we modify it. And this is true for BillaBear.

### Is it possible to have a customer that pays by invoice seperately?

Yes! BillaBear allows you to define how a customer pays which includes by invoice.

### Is it possible to disable customer creation for countries?

Yes! You're able to

### Is it possible to only collect tax for a country once we've met the Threshold?

Yes! BillaBear is aware of tax thresholds for countries and states.

You can also declare that you're collecting tax for a country even though you've not met the threshold.

### How much control over templates will I have?

Complete control. With the ability to define the templates using the Twig templating langage you're able to update them
with ease. You're also able to use email service provider's templating systems.

### Can I a trial that once it ends doesn't automatically convert to a proper subscription?

Yes! With BillaBear you can have an automatically converted trial or a standalone trial.

### Will I be able to see what subscriptions a payment is for?

Yes! BillaBear links payments to customers and the subscriptions they are for. As well as linking refunds from the
payments.

### Will I be able to handle tax rates changing?

Yes! BillaBear allows you to define the start and end date of a tax rule. This means you can define when a tax rate ends
and the next one takes over. So if a tax rate does change you're able to create the rule ahead of time and have it
applied correctly automatically when the time comes.

### Can I do usage based billing?

Yes.
