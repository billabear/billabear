<p align="center">
  <img width="450px" src="https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png">
</p>

<p align="center">
  <h1 style="text-align: center">BillaBear</h1>
</p>

BillaBear is a standalone Subscription Management and Billing System that integrates with Stripe. It provides a REST API allowing you to integrate it easily.

## Demo 

Watch the video on youtube.

[![Watch the video](https://img.youtube.com/vi/ByRwKryljSE/mqdefault.jpg)](https://youtu.be/ByRwKryljSE)

## Documentation

* [User Documentation](https://docs.billabear.com/user/) - How to use BillaBear as a user
* [Technical Documentation](https://docs.billabear.com/technical/) - Technical information for hosting and integration
* [Swagger](https://swagger.billabear.com) - The REST API docs for integration

## Features

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

```
git clone git@github.com:billabear/hosting-docker-compose.git
cd hosting-docker-compose
docker compose up -d
```

### Managed Cloud Hosting

You can get managed cloud hosting at https://billabear.com.

### Development

```
docker compose up -d
```

Then go to http://localhost and follow the install instructions.

## FAQ

### Is BillaBear Open Source?

It's available free to use under the Functional Software License that adds restrictions on competing for 2-years. 

There are arguments about if these licenses are open source or not, but generally for most we care can we use it for free and can we modify it. And this is true for BillaBear.

### Is it possible to have a customer that pays by invoice seperately?

Yes! BillaBear allows you to define how a customer pays which includes by invoice.

### Is it possible to disable customer creation for countries?

Yes! You're able to

### Is it possible to only collect tax for a country once we've met the Threshold?

Yes! BillaBear is aware of tax thresholds for countries and states. 

You can also declare that you're collecting tax for a country even though you've not met the threshold.

### How much control over templates will I have?

Complete control. With the ability to define the templates using the Twig templating langage you're able to update them with ease. You're also able to use email service provider's templating systems.

### Can I a trial that once it ends doesn't automatically convert to a proper subscription?

Yes! With BillaBear you can have an automatically converted trial or a standalone trial.

### Will I be able to see what subscriptions a payment is for?

Yes! BillaBear links payments to customers and the subscriptions they are for. As well as linking refunds from the payments.

### Will I be able to handle tax rates changing?

Yes! BillaBear allows you to define the start and end date of a tax rule. This means you can define when a tax rate ends and the next one takes over. So if a tax rate does change you're able to create the rule ahead of time and have it applied correctly automatically when the time comes.

### Can I do usage based billing?

No. Sadly, BillaBear doesn't support usage based billing yet. However, it will be added in the future. The same goes for tier pricing and stair pricing.
