<p align="center">
  <img width="450px" src="https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png">
</p>

<p align="center">
  <h1 style="text-align: center">BillaBear</h1>
</p>

Billing Made easy... Coming soon.

## Documentation

* [User Documentation](https://docs.billabear.com/docs/user/) - How to use BillaBear as a user
* [Technical Documentation](https://docs.billabear.com/docs/technical/) - Technical information for hosting and integration
* [Swagger](https://swagger.billabear.com) - The REST API docs for integration

## Features:

* Fully customisable Invoice, Receipt, and Email templates
* White Label Billing - Handle multiple brands
* Voucher Management
* REST API for integration
* Multiple Email Integrations - SendGrid, MailGun, and Postmark
* Expired Card Notifications - including ability to offer incentives to add a card.
* Multiple Subscriptions per customer
* Ability to have Custom Plans and Custom pricing
* Stripe Billing Integration
* Ability to Migrate away from Stripe Billing
* Invoice payment type for Enterprise customers

## RoadMap

* Webhooks to listen for billing events
* Tax Software Integrations
* Better Reports

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