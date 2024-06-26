Feature: Customer Subscription Read APP
  In order to stop billing unwilling customers
  As an API user
  I need to be able to cancel a subscription

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
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product Two | 4500   | USD      | true      | month    | true   |
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
    Given a Subscription Plan exists for product "Product Two" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 4500 in "USD" with:
      | Name       | Better Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |


  Scenario: Cancel subscription
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   |
      | customer.three@example.org | UK      | cust_dfsdfdsdu     | Customer three |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org   |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   |
      | Test Plan         | 3000         | USD            | month          | customer.three@example.org |
    When I cancel via the API the subscription "Test Plan" for "customer.one@example.org"
    Then the subscription "Test Plan" for "customer.one@example.org" will be cancelled
    And the monthly recurring revenue estimate should be 6000
    And the annual recurring revenue estimate should be 72000
    Then there should be a churn event for "customer.one@example.org"


  Scenario: Cancel subscription
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   |
      | customer.three@example.org | UK      | cust_dfsdfdsdu     | Customer three |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org   |
      | Better Test Plan  | 3000         | USD            | month          | customer.one@example.org   |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   |
      | Test Plan         | 3000         | USD            | month          | customer.three@example.org |
    When I cancel via the API the subscription "Test Plan" for "customer.one@example.org"
    Then the subscription "Test Plan" for "customer.one@example.org" will be cancelled
    Then there should be an add on removed event for "customer.one@example.org"
