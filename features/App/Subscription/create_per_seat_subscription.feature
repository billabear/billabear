Feature: Create per seat APP

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
      | Product One | 30000  | USD      | true      | year     | false  |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | True      |


  Scenario: Create subscription
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I create a subscription via the site for "customer.one@example.org" with the follow:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Seats |
      | Test Plan         | 3000         | USD            | month          | 5     |
    Then there should be a subscription for the user "customer.one@example.org"
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
    And the payment amount stats for the day should be 15000 in the currency "USD"
    And there is a subscription modification to add 5 seats to the subscription for "customer.one@example.org"
    And the subscription for "customer.one@example.org" has 5 seats