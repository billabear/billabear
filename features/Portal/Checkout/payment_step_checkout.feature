Feature: View checkout

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
      | Product One | 2000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | week     | false  |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 1000 in "USD" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 2000 in "USD" with:
      | Name       | Test Two |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 3000 in "USD" with:
      | Name       | Test Three |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 3000 in "USD" with:
      | Name       | Per Seat Plan |
      | Public     | True     |
      | Per Seat   | True    |
      | User Count | 10       |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         |
      | customer.three@example.org | UK      | cust_mlklfdu       | Customer Three | card         |
      | customer.four@example.org  | UK      | cust_dkkoadu       | Customer Four  | card         |
      | customer.five@example.org  | UK      | cust_ddsjfu        | Customer Five  | card         |
      | customer.six@example.org   | UK      | cust_jliujoi       | Customer Six   | card         |
      | customer.seven@example.org | UK      | cust_jliujoi       | Customer Six   | invoice      |

  Scenario: Pay permanent Checkout
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And a permanent checkout called "Test" exists in "USD":
      | Description | Total | Sub Total | Vat Total | Include Tax |
      | Setup costs | 12000 | 8000      | 4000      | True        |
    And I submit the customer in the portal checkout for "Test"
      | Email              | customer@example.org |
      | Country            | DE                   |
    When I enter the payment details in the portal checkout for "Test"
    And the payment amount stats for the day should be 12000 in the currency "USD"
    And the checkout "Test" will be valid

  Scenario: Pay temporary Checkout
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And a temporary checkout called "Test" exists in "USD":
      | Description | Total | Sub Total | Vat Total | Include Tax |
      | Setup costs | 12000 | 8000      | 4000      | True        |
    And I submit the customer in the portal checkout for "Test"
      | Email              | customer@example.org |
      | Country            | DE                   |
    When I enter the payment details in the portal checkout for "Test"
    And the payment amount stats for the day should be 12000 in the currency "USD"
    And the checkout "Test" will not be valid