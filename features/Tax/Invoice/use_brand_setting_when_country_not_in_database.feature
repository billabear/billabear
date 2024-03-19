Feature: Use brand setting when country not in database

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
    And the follow brands exist:
      | Name    | Code                    | Email               | Country | Tax Rate | Digital Services Tax Rate |
      | Example | example                 | example@example.org | ZA      | 12       | 22                        |
      | Example | no_digital_services_tax | example@example.org | ZA      | 15       |                           |
    And the follow products exist:
      | Name          | External Reference | Tax Type         |
      | Product One   | prod_jf9j545       | Digital Goods    |
      | Product Two   | prod_jf9j542       | Digital Services |
      | Product Three | prod_jf9j542       | Physical |
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
    Given a Subscription Plan exists for product "Product Two" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Digital Services |
      | Public     | True             |
      | Per Seat   | False            |
      | User Count | 10               |
    Given a Subscription Plan exists for product "Product Three" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Physical Plan |
      | Public     | True             |
      | Per Seat   | False            |
      | User Count | 10               |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type | Payment Reference | Tax Number | Digital Tax Rate | Standard Tax Rate |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      | null              | FJDKSLfjdf | 10               | 15                |
      | customer.two@example.org   | GB      | cust_dfugfdu       | Customer Two   | card         | ref_valid         | ssdfds     |                  |                   |
      | customer.three@example.org | GB      | cust_mlklfdu       | Customer Three | card         | ref_valid         | gfdgsfd    |                  |                   |
      | customer.four@example.org  | GB      | cust_dkkoadu       | Customer Four  | card         | ref_fails         | 35435 43   |                  |                   |
      | customer.five@example.org  | GB      | cust_ddsjfu        | Customer Five  | card         | ref_valid         | dfadf      |                  |                   |
      | customer.six@example.org   | GB      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     |                  |                   |


  Scenario: Standard Tax Rate
    Given the follow customers exist:
      | Email                      | Country | External Reference | Reference    | Billing Type | Payment Reference | Brand   |
      | customer.south@example.org | ZA      | cust_dfugfdu       | Customer Two | card         | ref_valid         | example |
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Physical Plan     | 1000         | USD            | week           | customer.south@example.org | +3 Minutes  | Active |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.south@example.org" will have tax country of "ZA"
    And there the latest invoice for "customer.south@example.org" will have tax rate of 12

  Scenario: Digital Services Tax Rate
    Given the follow customers exist:
      | Email                      | Country | External Reference | Reference    | Billing Type | Payment Reference | Brand   |
      | customer.south@example.org | ZA      | cust_dfugfdu       | Customer Two | card         | ref_valid         | example |
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Digital Services  | 1000         | USD            | week           | customer.south@example.org | +3 Minutes  | Active |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.south@example.org" will have tax country of "ZA"
    And there the latest invoice for "customer.south@example.org" will have tax rate of 22

  Scenario: Digital Services Tax Rate not set use standard tax rate
    Given the follow customers exist:
      | Email                      | Country | External Reference | Reference    | Billing Type | Payment Reference | Brand                   |
      | customer.south@example.org | ZA      | cust_dfugfdu       | Customer Two | card         | ref_valid         | no_digital_services_tax |
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Digital Services  | 1000         | USD            | week           | customer.south@example.org | +3 Minutes  | Active |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.south@example.org" will have tax country of "ZA"
    And there the latest invoice for "customer.south@example.org" will have tax rate of 15