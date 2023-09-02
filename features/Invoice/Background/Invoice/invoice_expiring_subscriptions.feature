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
      | Email                      | Country | External Reference | Reference      | Billing Type | Payment Reference | Tax Number |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      | null              | FJDKSLfjdf |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         | ref_valid         | ssdfds     |
      | customer.three@example.org | UK      | cust_mlklfdu       | Customer Three | card         | ref_valid         | gfdgsfd    |
      | customer.four@example.org  | UK      | cust_dkkoadu       | Customer Four  | card         | ref_fails         | 35435 43   |
      | customer.five@example.org  | UK      | cust_ddsjfu        | Customer Five  | card         | ref_valid         | dfadf      |
      | customer.six@example.org   | UK      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     |


  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the subscription for "customer.two@example.org" will expire in a month
    And the subscription for "customer.three@example.org" will expire in a year
    But the subscription for "customer.four@example.org" will expire today
    And the subscription for "customer.five@example.org" will expire today
    And the subscription for "customer.six@example.org" will expire today
    And the payment amount stats for the day should be 33000 in the currency "USD"

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Active |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.four@example.org" will expire in a week
    And there the latest invoice for "customer.four@example.org" will not be marked as paid
    And there is a payment attempt for "customer.four@example.org" will exist

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the subscription for "customer.two@example.org" will expire today
    And the subscription for "customer.three@example.org" will expire today
    But the subscription for "customer.four@example.org" will expire today
    And the subscription for "customer.five@example.org" will expire today
    And the subscription for "customer.six@example.org" will expire today

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    And the following credit transactions exist:
      | Customer                 | Type   | Amount | Currency |
      | customer.one@example.org | credit | 100   | USD      |
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the latest invoice for "customer.one@example.org" will have amount due as 900

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    And the following credit transactions exist:
      | Customer                 | Type   | Amount | Currency |
      | customer.one@example.org | credit | 1100  | USD      |
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the latest invoice for "customer.one@example.org" will have amount due as 0
    And the credit amount for "customer.one@example.org" should be 100

  Scenario:
    Given that the tax settings for tax customers with tax number is true
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    And the following credit transactions exist:
      | Customer                 | Type   | Amount | Currency |
      | customer.one@example.org | debit  | 100   | USD      |
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the latest invoice for "customer.one@example.org" will have amount due as 1100
    And the latest invoice for "customer.one@example.org" will have tax amount due

  Scenario: No tax
    Given that the tax settings for tax customers with tax number is false
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    And the following credit transactions exist:
      | Customer                 | Type   | Amount | Currency |
      | customer.one@example.org | debit  | 100   | USD      |
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the latest invoice for "customer.one@example.org" will have amount due as 1100
    And the latest invoice for "customer.one@example.org" will not have tax amount due

  Scenario: Has voucher
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    And the following vouchers exist:
      | Name        | Type         | Entry Type | Code | Percentage Value | USD  | GBP | Disabled |
      | Voucher One | Percentage   | Automatic  | n/a  | 50               | n/a  | n/a | false    |
    And the customer "customer.one@example.org" has the voucher "Voucher One" applied
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the latest invoice for "customer.one@example.org" will have amount due as 500

  Scenario: Has voucher that was used
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    And the following vouchers exist:
      | Name        | Type         | Entry Type | Code | Percentage Value | USD  | GBP | Disabled |
      | Voucher One | Percentage   | Automatic  | n/a  | 50               | n/a  | n/a | false    |
    And the customer "customer.one@example.org" has the voucher "Voucher One" applied that has been used
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the latest invoice for "customer.one@example.org" will have amount due as 1000


  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |
    And stripe billing is enabled
    And the following credit transactions exist:
      | Customer                 | Type   | Amount | Currency |
      | customer.one@example.org | credit | 100   | USD      |
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And the latest invoice for "customer.one@example.org" will have amount due as 900
    And the latest invoice for "customer.one@example.org" will be due in "30 days"