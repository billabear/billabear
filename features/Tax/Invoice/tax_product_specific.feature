Feature: Use Product Tax Rate

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow products exist:
      | Name        | External Reference | Tax Rate | Tax Type |
      | Product One | prod_jf9j545       | 25       | NULL     |
      | Product Two | prod_jf9j542       |          |          |
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
      | Email                      | Country | External Reference | Reference      | Billing Type | Payment Reference | Tax Number |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      | null              | FJDKSLfjdf |
      | customer.two@example.org   | GB      | cust_dfugfdu       | Customer Two   | card         | ref_valid         | ssdfds     |
      | customer.three@example.org | GB      | cust_mlklfdu       | Customer Three | card         | ref_valid         | gfdgsfd    |
      | customer.four@example.org  | GB      | cust_dkkoadu       | Customer Four  | card         | ref_fails         | 35435 43   |
      | customer.five@example.org  | GB      | cust_ddsjfu        | Customer Five  | card         | ref_valid         | dfadf      |
      | customer.six@example.org   | GB      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     |


  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.one@example.org" will have tax rate of 25
