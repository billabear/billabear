Feature: Generate new invoices

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
      | Per Seat   | False     |
      | User Count | 10        |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Two |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   |
      | customer.three@example.org | UK      | cust_mlklfdu       | Customer Three |
      | customer.four@example.org  | UK      | cust_dkkoadu       | Customer Four  |
      | customer.five@example.org  | UK      | cust_ddsjfu        | Customer Five  |
      | customer.six@example.org   | UK      | cust_jliujoi       | Customer Six   |


  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the subscription for "customer.two@example.org" will expire in a month
    And the subscription for "customer.three@example.org" will expire in a year
    But the subscription for "customer.four@example.org" will expire today
    And the subscription for "customer.five@example.org" will expire today
    And the subscription for "customer.six@example.org" will expire today
    And the payment amount stats for the day should be 34000 in the currency "USD"