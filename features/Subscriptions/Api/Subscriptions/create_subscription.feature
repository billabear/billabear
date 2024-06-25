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
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
      | Product One | 50000  | USD      | false     |          | false  |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "3000" in "USD" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
      | Code Name  | test_plan |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "3000" in "USD" with:
      | Name       | Trial Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
      | Code Name  | trial_plan |
      | Standalone Trial | true      |


  Scenario: Create
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Test Plan         | 3000         | USD            | month          |
    Then there should be a subscription for the user "customer.one@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the payment amount stats for the day should be 3000 in the currency "USD"
    And the monthly recurring revenue estimate should be 3000
    And the annual recurring revenue estimate should be 36000
    Then there should be an activated event for "customer.one@example.org"

  Scenario: Create one off
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency |
      | Test Plan         | 50000        | USD            |
    Then there should be a subscription for the user "customer.one@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the payment amount stats for the day should be 50000 in the currency "USD"
    And the monthly recurring revenue estimate should be 0
    And the annual recurring revenue estimate should be 0


  Scenario: Create using code name
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription with code and currency via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Currency | Price Schedule |
      | test_plan         | USD            | month          |
    Then there should be a subscription for the user "customer.one@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the payment amount stats for the day should be 3000 in the currency "USD"
    And the monthly recurring revenue estimate should be 3000
    And the annual recurring revenue estimate should be 36000

  Scenario: Create using code name
    Given I have authenticated to the API
    And stripe billing is disabled
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription with code and currency via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Currency | Price Schedule |
      | test_plan         | USD            | month          |
    Then there should be a subscription for the user "customer.one@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the payment amount stats for the day should be 3000 in the currency "USD"
    And the monthly recurring revenue estimate should be 3000
    And the annual recurring revenue estimate should be 36000

  Scenario: Create using code name - fails no such plan
    Given I have authenticated to the API
    And stripe billing is disabled
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription with code and currency via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Currency | Price Schedule |
      | test_plan_two     | USD            | month          |
    Then there should not be a subscription for the user "customer.one@example.org"
    And there should be an error for "subscription_plan"

  Scenario: Create Failure
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Payment Reference |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | ref_fails         |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |                   |
    When I create a subscription with code and currency via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Currency | Price Schedule |
      | test_plan         | USD            | month          |
    Then there should not be a subscription for the user "customer.one@example.org"
    And the response should be that payment is needed

  Scenario: Create Failure - stripe billing
    Given I have authenticated to the API
    And stripe billing is disabled
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Payment Reference |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | ref_fails         |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |                   |
    When I create a subscription with code and currency via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Currency | Price Schedule |
      | test_plan         | USD            | month          |
    Then there should not be a subscription for the user "customer.one@example.org"
    And the response should be that payment is needed

  Scenario: Addon Added event
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
    When I create a subscription via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Test Plan         | 3000         | USD            | month          |
    Then there should be an add-on added event for "customer.one@example.org"

  Scenario: Create trial
    Given I have authenticated to the API
    And stripe billing is disabled
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Trial Plan        | 3000         | USD            | month          |
    Then there should be a subscription for the user "customer.one@example.org"
    And the trial started daily stat for the day should be 1
    And the trial started monthly stat for the day should be 1
    And the trial started yearly stat for the day should be 1
    And the subscriber daily stat for the day should be 0
    And the subscriber monthly stat for the day should be 0
    And the subscriber yearly stat for the day should be 0
    And the payment amount stats for the day should be 0 in the currency "USD"
    And the monthly recurring revenue estimate should be 0
    And the annual recurring revenue estimate should be 0
    Then there should be a trial started event for "customer.one@example.org"

  Scenario: Create trial - Deny
    Given I have authenticated to the API
    And stripe billing is disabled
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Deny Trial |
      | Trial Plan        | 3000         | USD            | month          | true       |
    Then there should be a subscription for the user "customer.one@example.org"
    And the trial started daily stat for the day should be 0
    And the trial started monthly stat for the day should be 0
    And the trial started yearly stat for the day should be 0
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the payment amount stats for the day should be 3000 in the currency "USD"
    And the monthly recurring revenue estimate should be 3000
    And the annual recurring revenue estimate should be 36000
    Then there should not be a trial started event for "customer.one@example.org"

  Scenario: Reactivated event
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Status    | Started Current Period | Next Charge |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | Cancelled | -14 months             | -2 months   |
    When I create a subscription via the API for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule |
      | Test Plan         | 3000         | USD            | month          |
    Then there should be a reactivated event for "customer.one@example.org"
