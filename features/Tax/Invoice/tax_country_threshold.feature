Feature: Tax the correct country with threshold

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And that the tax settings for tax customers with tax number is true
    And the follow brands exist:
      | Name    | Code                    | Email               | Country |
      | Example | example                 | example@example.org | GB      |
    And there are the following tax types:
      | Name             | Physical |
      | Digital Goods    | False    |
      | Digital Services | False    |
      | Physical         | True     |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Transaction Threshold | Currency |
      | United Kingdom | GB       | 1770      |                       | GBP      |
    And the following country tax rules exist:
      | Country        | Tax Type       | Tax Rate | Valid From |
      | United Kingdom | Digital Goods  | 20       | -10 days   |
    And the follow products exist:
      | Name        | External Reference | Tax Type      |
      | Product One | prod_jf9j545       | Digital Goods |
      | Product Two | prod_jf9j542       | Digital Goods |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Two |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type | Payment Reference | Tax Number | Brand   |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      | null              | FJDKSLfjdf | example |
      | customer.two@example.org   | GB      | cust_dfugfdu       | Customer Two   | card         | ref_valid         | ssdfds     | example |
      | customer.three@example.org | GB      | cust_mlklfdu       | Customer Three | card         | ref_valid         | gfdgsfd    | example |
      | customer.four@example.org  | GB      | cust_dkkoadu       | Customer Four  | card         | ref_fails         | 35435 43   | example |
      | customer.five@example.org  | GB      | cust_ddsjfu        | Customer Five  | card         | ref_valid         | dfadf      | example |
      | customer.six@example.org   | GB      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     | example |
      | customer.seven@example.org | US      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     | example |
      | customer.eight@example.org | AU      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     | example |

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.seven@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name          | ISO Code | Threshold | Currency |
      | United States | US       | 0         | USD      |
    And the following country tax rules exist:
      | Country       | Tax Type       | Tax Rate | Valid From |
      | United States | Digital Goods  | 17.5     | -10 days   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.seven@example.org" will have tax country of "US"

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.seven@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United States  | US       | 1000000   | USD      |
      | United Kingdom | GB       | 1000000   | GBP      |
    And the following country tax rules exist:
      | Country       | Tax Type       | Tax Rate | Valid From |
      | United States | Digital Goods  | 17.5     | -10 days   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.seven@example.org" will have tax country is empty

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.seven@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency | Collecting |
      | United States  | US       | 1000000   | USD      | False      |
      | United Kingdom | GB       | 1000000   | GBP      | True       |
    And the following country tax rules exist:
      | Country       | Tax Type       | Tax Rate | Valid From |
      | United States | Digital Goods  | 17.5     | -10 days   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.seven@example.org" will have tax country of "GB"

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.seven@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Transaction Threshold | Currency |
      | United States  | US       | 1000000   |                       | USD      |
      | United Kingdom | GB       | 0         |                       | USD      |
      | Australia      | AU       | 7500000   | 100                   | AUD      |
    And the following country tax rules exist:
      | Country       | Tax Type       | Tax Rate | Valid From |
      | United States | Digital Goods  | 17.5     | -10 days   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.seven@example.org" will have tax country of "GB"

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.seven@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Transaction Threshold | Currency |
      | United States  | US       | 1000000   |                       | USD      |
      | United Kingdom | GB       | 0         |                       | USD      |
      | Australia      | AU       | 7500000   | 100                   | AUD      |
    And the following country tax rules exist:
      | Country       | Tax Type       | Tax Rate | Valid From |
      | United States | Digital Goods  | 17.5     | -10 days   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.seven@example.org" will have tax country of "GB"

  Scenario: Country transaction threshold not reached
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.eight@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Transaction Threshold | Currency |
      | United States  | US       | 1000000   |                       | USD      |
      | United Kingdom | GB       | 0         |                       | USD      |
      | Australia      | AU       | 7500000   | 100                   | AUD      |
    And the following country tax rules exist:
      | Country       | Tax Type       | Tax Rate | Valid From |
      | Australia     | Digital Goods  | 20       | -10 days   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.eight@example.org" will have tax country of "GB"

  Scenario: Country transaction threshold reached
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.eight@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Transaction Threshold | Currency |
      | United States  | US       | 1000000   |                       | USD      |
      | United Kingdom | GB       | 0         |                       | USD      |
      | Australia      | AU       | 7500000   | 100                   | AUD      |
    And the following country tax rules exist:
      | Country       | Tax Type       | Tax Rate | Valid From |
      | Australia     | Digital Goods  | 20       | -10 days   |
    And there are 200 transactions for "customer.eight@example.org" for the past 12 months
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.eight@example.org" will have tax country of "AU"