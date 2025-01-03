Feature: Customer Subscription Create APP
  In order to manage a customer's subscriptions
  As an API user
  I need to be see customer's Subscription

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |
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


  Scenario: Create subscription
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription via the site for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Test Plan         | 3000         | USD            | month          |
    Then there should be a subscription for the user "customer.one@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the payment amount stats for the day should be 3000 in the currency "USD"


  Scenario: Create subscription - Invoice
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | invoice      |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two | card         |
    When I create a subscription via the site for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Test Plan         | 3000         | USD            | month          |
    Then there should be a subscription for the user "customer.one@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the external references for the subscription for the user "customer.one@example.org" should be blank

  Scenario: Get subscription - Invoice and stripe billing is disabled
    Given stripe billing is disabled
    And I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | invoice      |
      | customer.two@example.org | GB      | cust_dfugfdu       | Customer Two | card         |
    When I create a subscription via the site for "customer.two@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Test Plan         | 3000         | USD            | month          |
    Then there should be a subscription for the user "customer.two@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the external references for the subscription for the user "customer.two@example.org" should be blank
    And the payment amount stats for the day should be 3000 in the currency "USD"

  Scenario: Get subscription - Invoice and stripe billing is disabled
    Given stripe billing is disabled
    And I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | invoice      |
      | customer.two@example.org |         | cust_dfugfdu       | Customer Two | card         |
    When I create a subscription via the site for "customer.two@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Test Plan         | 3000         | USD            | month          |
    Then there should be a subscription for the user "customer.two@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the external references for the subscription for the user "customer.two@example.org" should be blank
    And the payment amount stats for the day should be 3000 in the currency "USD"

  Scenario: Create Subscription - graduated tiered usage continuous with previous invoice
    Given the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | invoice      |
      | customer.two@example.org |         | cust_dfugfdu       | Customer Two | card         |
    Given the following invoices exist:
      | Customer                 | Paid  |
      | customer.two@example.org | false |
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Continuous | Sum                |                      | Real Time |
    Given the follow price "Product One" for "USD" monthly price with a usage tiered graduated for a continuous metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 0          | 0        |
      | 21         | -1        | 0.01       | 0        |
    Given stripe billing is disabled
    And I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a subscription via the site for "customer.two@example.org" with the follow:
      | Subscription Plan | Type             | Price Currency | Price Schedule |
      | Test Plan         | tiered_graduated | USD            | month          |
    Then there should be a subscription for the user "customer.two@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the external references for the subscription for the user "customer.two@example.org" should be blank
    And the payment amount stats for the day should be 0 in the currency "USD"
