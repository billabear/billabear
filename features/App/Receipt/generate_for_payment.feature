Feature: Payment List Read APP
  In order to manage a payments
  As an APP user
  I need to be see all the payments

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
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Two  |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |


  Scenario: Get customer info
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org |
    And there is a payments for:
      | Subscription Plan | Customer                 | Amount |
      | Test Plan         | customer.one@example.org | 3500   |
    When I generate a receipt for the payment for "customer.one@example.org" for 3500
    Then then there will be a receipt for "customer.one@example.org" for 3500
