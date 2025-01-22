Feature: Customer Subscription Update Plan

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And there are the following tax types:
      | Name             | Physical |
      | Digital Goods    | False    |
      | Digital Services | False    |
      | Physical         | True     |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency | In EU |
      | United Kingdom | GB       | 1770      | GBP      | False |
      | United States  | US       | 0         | USD      | False |
      | Germany        | DE       | 0         | EUR      | True  |
    And the following country tax rules exist:
      | Country        | Tax Type      | Tax Rate | Valid From |
      | United States  | Digital Goods | 17.5     | -10 days   |
      | United Kingdom | Digital Goods | 17.5     | -10 days   |
      | Germany        | Digital Goods | 17.5     | -10 days   |
    And the follow products exist:
      | Name          | External Reference |
      | Product One   | prod_jf9j545       |
      | Product Two   | prod_jf9j542       |
      | Product Three | prod_jf9jk42       |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 3500   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
      | Product Two | 4500   | USD      | true      | month    | true   |
      | Product Two | 4000   | EUR      | true      | month    | true   |
      | Product Three | 5500   | USD      | true      | month    | true   |
      | Product Three | 55000   | USD      | true      | year    | true   |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 3500 in "USD" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    Given a Subscription Plan exists for product "Product Two" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 4500 in "USD" with:
      | Name       | Better Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    Given a Subscription Plan exists for product "Product Three" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 5500 in "USD" monthly and 55000 yearly with:
      | Name       | Even Better Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |


  Scenario: Update Plan
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year |
      | customer.one@example.org | 0444      | 03           | 25          |
    When I make a API Request to update the subscription "Test Plan" for "customer.one@example.org" to plan:
      | Product | Product Two      |
      | Plan    | Better Test Plan |
      | Price   | 4500             |
      | Currency| USD              |
    Then the subscription "Test Plan" for "customer.one@example.org" will not exist
    And the subscription "Better Test Plan" for "customer.one@example.org" will exist

  Scenario: Update Plan - Stripe Billing Disable
    Given I have authenticated to the API
    And stripe billing is disabled
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year |
      | customer.one@example.org | 0444      | 03           | 25          |
    When I make a API Request to update the subscription "Test Plan" for "customer.one@example.org" to plan to be changed instantly:
      | Product | Product Two      |
      | Plan    | Better Test Plan |
      | Price   | 4500             |
      | Currency| USD              |
    Then the subscription "Test Plan" for "customer.one@example.org" will not exist
    And the subscription "Better Test Plan" for "customer.one@example.org" will exist
    And the latest invoice for "customer.one@example.org" will have amount due as 1500

  Scenario: Update Plan - Stripe Billing Disable
    Given I have authenticated to the API
    And stripe billing is disabled
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Better Test Plan  | 4500         | USD            | month          | customer.one@example.org |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year |
      | customer.one@example.org | 0444      | 03           | 25          |
    When I make a API Request to update the subscription "Better Test Plan" for "customer.one@example.org" to plan to be changed instantly:
      | Product | Product One |
      | Plan    | Test Plan   |
      | Price   | 3000        |
      | Currency| USD         |
    Then the subscription "Better Test Plan" for "customer.one@example.org" will not exist
    And the subscription "Test Plan" for "customer.one@example.org" will exist
    Then there should be a credit for "customer.one@example.org" for 1500 in the currency "USD"

  Scenario: Usage based change price
    Given I have authenticated to the API
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Sum                |                      | Real Time |
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    Given the follow price "Product One" for "USD" monthly price with a usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 0          | 1000     |
      | 21         | -1        | 0          | 1500     |
    And the a price for "Product One" for 250 "USD" monthly price for package of 2500 units for metric "Test One"
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | month          | customer.one@example.org | +3 days     | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       |
    And stripe billing is disabled
    When I make a API Request to update the subscription "Test Plan" for "customer.one@example.org" to plan to be changed instantly:
      | Product    | Product One |
      | Plan       | Test Plan   |
      | Price Type | package     |
      | Currency   | USD         |
    Then there will be an invoice for "customer.one@example.org" with a metric counter "Test One"
