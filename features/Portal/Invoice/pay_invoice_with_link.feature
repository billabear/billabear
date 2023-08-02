Feature: Pay invoices with link

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
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         |
      | customer.three@example.org | UK      | cust_mlklfdu       | Customer Three | card         |
      | customer.four@example.org  | UK      | cust_dkkoadu       | Customer Four  | card         |
      | customer.five@example.org  | UK      | cust_ddsjfu        | Customer Five  | card         |
      | customer.six@example.org   | UK      | cust_jliujoi       | Customer Six   | card         |
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |

  Scenario: Unpaid
    Given the following invoices exist:
      | Customer                 | Paid  | Invoice Number |
      | customer.one@example.org | false | BB-304303      |
    When I go to the invoice paylink for invoice "BB-304303"
    Then I should see the total amount
    And I should see the stripe frontend

  Scenario: Paid
    Given the following invoices exist:
      | Customer                 | Paid | Invoice Number |
      | customer.one@example.org | true | BB-304303      |
    When I go to the invoice paylink for invoice "BB-304303"
    Then I should see that the invoice has been paid