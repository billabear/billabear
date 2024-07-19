Feature: Extend trial subscription

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
      | Subscription Plan | Customer                   | Status       | Next Charge |
      | Test Plan         | customer.one@example.org   | trial_active | +3 days     |
    When I extend via the API the subscription "Test Plan" for "customer.one@example.org" with the follow:
     | Price Amount   | 3000  |
     | Price Currency | USD   |
     | Price Schedule | month |
    Then the subscription "Test Plan" for "customer.one@example.org" will be active
    Then there should be a trial converted event for "customer.one@example.org"
    And the trial extended daily stat for the day should be 1
    And the trial extended monthly stat for the day should be 1
    And the trial extended yearly stat for the day should be 1
    And the subscriber daily stat for the day should be 1
    And the subscriber monthly stat for the day should be 1
    And the subscriber yearly stat for the day should be 1
