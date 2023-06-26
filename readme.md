<p align="center">
  <img width="450px" src="https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png">
</p>

<p align="center">
  <h1 style="text-align: center">BillaBear</h1>
</p>

BillaBear is a standalone Subscription Management and Billing System that integrates with Stripe. It provides a REST API allowing you to integrate it easily.

[![Watch the video](https://img.youtube.com/vi/cbInyGtqLCs/default.jpg)](https://youtu.be/cbInyGtqLCs)


## Documentation

* [User Documentation](https://docs.billabear.com/user/) - How to use BillaBear as a user
* [Technical Documentation](https://docs.billabear.com/technical/) - Technical information for hosting and integration
* [Swagger](https://swagger.billabear.com) - The REST API docs for integration

## Features:

* Fully customisable Invoice, Receipt, and Email templates
* White Label Billing - Handle multiple brands
* Voucher Management
* REST API for integration
* Multiple Email Integrations - SendGrid, MailGun, and Postmark
* Expired Card Notifications - including the ability to offer incentives to add a card.
* Multiple Subscriptions per customer - Add add-ons.
* Ability to have Custom Plans and Custom pricing
* Stripe Billing Data Import
* Stripe Billing Integration
* Ability to Migrate away from Stripe Billing
* Invoice payment type for Enterprise customers

## Lifetime Deal

Until the 1st of July we're offering a lifetime upgrade license for a one-off fee of  250 GBP. [Buy a lifetime upgrade license here](https://buy.stripe.com/4gweY33GnaNP8daeUU).

This offer must end on the 1st of July as we'll start approaching companies asking for thousands per year so this offer must end.

What do you get? A license to use BillaBear in production and priority customer support forever.

[Buy now](https://buy.stripe.com/4gweY33GnaNP8daeUU).

## RoadMap

* Webhooks to listen for billing events
* Tax Software Integrations
* Better Reports
* Slack/etc integration for internal communications
* Per Seat billing
* Metric and Usage Based Billing
* [And you can add more here](https://github.com/billabear/billabear/discussions/categories/ideas)



## How to Integrate

To start integrating with BillaBear you can use the REST API.

* [Swagger Docs](https://swagger.billabear.com)
* [Technical Documentation For API](https://docs.billabear.com/technical/api/)

## Getting Started

To get started using this repository you can get up and running using Docker and Docker compose.

### Host on DigitalOcean

You can deploy to DigitalOcean with just a click of the button below and it'll deploy using the DigitalOcean App Platform.

[![Deploy to DO](https://www.deploytodo.com/do-btn-blue.svg)](https://cloud.digitalocean.com/apps/new?repo=https://github.com/billabear/billabear/tree/main)

### Docker Compose

Billabear is deployable using docker-compose using the docker-compose.yaml found in https://github.com/billabear/hosting-docker-compose.

### Development

```
docker compose up -d
```

Then go to http://localhost and follow the install instructions.

## FAQ

### Is BillaBear Open Source?

No. BillaBear is released under the source-available license Business Source License. You can find it in the license.md. This is the same license as used by Sentry, CoackroachDB, and many more.

After 3-years from the release date of a version, it will then become open-source.

### How does the licensing work?

You're able to use BillaBear for free in non-production environments. Any use of a production Stripe API key would be considered a production environment.

You're able to use BillaBear for free if you're generating less than 5,000 USD a month. Otherwise, a paid license is required.

We sell perpetual licenses for the version that was released on the date of purchase as well as upgrade licenses that allow upgrading to newer versions. If you buy a license and never wish to upgrade then you pay once and never again.

### How much does BillaBear cost?

To get pricing email sales@billabear.com.

Until the 1st of July we're offering a lifetime upgrade license for a one-off fee of  250 GBP. [Buy a lifetime upgrade license here](https://buy.stripe.com/4gweY33GnaNP8daeUU).

This offer must end on the 1st of July as we'll start approaching companies asking for thousands per year so this offer must end.

What do you get? A license to use BillaBear in production and priority customer support forever.

[Buy now](https://buy.stripe.com/4gweY33GnaNP8daeUU).
