<p align="center">
  <img width="450px" src="https://getparthenon.com/images/logo.svg">
</p>

<p align="center"><strong>Parthenon Skeleton Application</strong></p>

A quick and easy way to start your next application.

## Get started

To get started you can either click the use template button \([click here](https://github.com/getparthenon/skeleton/generate)\) on GitHub that will create a repository for you which you can clone and use.

Or via composer using the `create-project` command.

`composer create-project parthenon/skeleton new-application`

## Local Development Environment

Requirements:

* docker
* yarn
* php 8.1
* composer

### Setup

The first step is to set up the environment.

```
composer install
yarn install
cd docker
docker-compose up -d
cd ..
bin/console doctrine:migrations:migrate
```

### Run

To start up the local development environment you

```
cd docker
docker-compose up -d
cd ..
yarn encore dev-server
```

Then visit https://localhost


## Documentation

The documentation can be found on the Parthenon website https://getparthenon.com/docs/getting-started/.

If you wish to contribute to the documentation. Or just look at the raw files. They can be found at https://github.com/getparthenon/parthenon-docs.

## Support

Support is provided via GitHub, Slack, and Email.

If you have a commercial license you will be able to list the GitHub accounts that you want to link to the license. This
means when an issue is created by an account linked to a commercial license they will get priority support. All other
issues will be given best effort support.

* Github: You can make an issue on [getparthenon/monorepo](https://github.com/getparthenon/monorepo/issues/new/choose)
* Email: support@getparthenon.com
* Slack: [Click here](https://join.slack.com/t/parthenonsupport/shared_invite/zt-1gujl7xsw-OALGFlPs~_Vf1cw6zaEkdg) to signup

Issues we will provide support and fixes for:

* Defects/Bugs
* Performance issues
* Documentation fixes/improvements
* Lack of flexibility
* Feature requests

## FAQ

### Is Parthenon Open-Source?

Parthenon is not considered to be open-source; it's made available under a license type known as a source available license. The License Parthenon is released under is the Business Source License which was created by MariaDB. The premise is you're allowed to use Parthenon free of charge for non-production use but are required to have a commercial license if you use it in production and generate more than $5,000 USD a month.

### Can I use Parthenon for free?

A quick answer is that you can use Parthenon for free if you're generating less $5,000 USD a month.

For non-production use, you can use Parthenon for free. This includes development and testing. If you're generating less than $5,000 USD a month in revenue you can use Parthenon for free. Otherwise, you'll need a commercial license to use it in production.

### Where can I get a commercial license?

You can find out more information about getting a commercial license at https://getparthenon.com or email iain@humblyarrogant.io.

### How much does a commercial license cost?

The cost of a commercial license is $250 per developer.

### Do I get better support when I have a commercial license?

Yes. Bugs report, support requests, and feature requests from commercial license holders will receive preferential treatment and will always receive a response.

Those without a commercial license will receive better efforts to support them.

### Do I get a perpetual commercial license for Parthenon?

When you get a license, you have a perpetual license for the minor version of Parthenon that is currently released at the time of purchase, as well as the right to receive updates for a year. If you subscribe to the one-year update support, you'll be able to always use the latest version. If you choose not to continue an updates subscription, you'll be entitled to use the minor version of Parthenon that was available at the last yearly license update.

For example, if you buy a commercial license and the minor version is 2.0, and you don't subscribe to a yearly subscription, you'll be able to use 2.0 and any minor version released throughout the year, but once the update support has ended, you'll be entitled to use 2.0 only.

Another example: if you buy a commercial license and the minor version is 2.0 and subscribe for three years, and after the support license has ended, you would be entitled to use the minor version that was available 12 months before. So if 2.4 was released at the start of the final year of update support and 2.5 was available at the end of it, you would be entitled to use version 2.4 after the support license is over.

### Who is Parthenon for?

Parthenon is for people who want to operate a web company that doesn't focus on the boring tech that everyone has done.

From bootstraps that want to start their business on the right footing to companies that want to improve their tech to large companies that have new projects and don't want to rebuild the same features they've done so many times.

### Can I use Parthenon with my existing Symfony application?

Yes. Parthenon is a bundle that can be used with your existing Symfony application. All the modules are toggable. So if you only want to use one part, you can.

### Will I be able to grow with Parthenon?

Parthenon is designed to scale. It has been purposefully designed so that things are able to be replaced as you grow.

We know that as your system scales, there will be parts of Parthenon you'll want to replace with highly custom code, and we designed Parthenon to allow you to do it with ease.

### What happens if the development of Parthenon stops?

Because we've licensed Parthenon under the Business Source License, after three years, the release will be licensed under a FOSS license which means the community will be able to take over if anything unforeseen happens.
