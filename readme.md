<p align="center">
  <img width="450px" src="https://ha-static-data.s3.eu-central-1.amazonaws.com/github-readme-logo.png">
</p>

<p align="center">
  <h1 style="text-align: center">BillaBear</h1>
</p>

Billing Made easy... Coming soon.

## Features:

### White Label Billing

You'll be able to bill customers in a white label fashion, enabling you to white label your applications a lot easier.

You'll be able to define which brand a customer belongs to and all emails will be sent using the correct brand template. Removing tedious work when selling a white label version for resell.

### Support Enterprise Customers 

You'll be able to handle enterprise customers a lot easier. With the ability to define a customer as having an payment type of invoice will allow you to onboard and conform with enterprise billing requirements.

You'll be able to create custom plans with custom limits for Enterprise customers to meet their specific requirements.

### Dunning/Payment Reminders

The ability to define how payment reminds and payment attempts are done after a payment has failed.

### Expiring Card Handling

You'll be able to reduce your customer churn related to expired cards by sending notifications.

Notifications:

* Start of the month to tell them that it's expired
* The day before the payment while the card is still meant to be valid.
* The day before the next payment after the card has expired.

You'll be able to decrease churn further by offering credit for customers who update their card after receiving a notification that their payment method will expire.

### Stripe Billing Integration

You'll be able to continue using the Stripe Billing functionality that you love. While being able to take advantage of all other features.

Or if you're looking to save costs, you can easily migrate away from Stripe Billing with the simple click of a button and let BillaBear handle it.

### Subscription Management

You'll be able to quickly and easy manage subscriptions.

* Easily change prices for subscriptions including moving existing Stripe Billing subscriptions to a new price.
* Move all existing subscriptions to a new plan.

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