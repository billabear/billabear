Feature: Update Subscription Plan

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
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 100   | USD      | true      | week     | true   |
      | Product One | 120   | USD      | true      | week     | true   |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan  |
      | Public     | True       |
      | Per Seat   | False      |
      | User Count | 10         |
      | Code Name  | test       |
    When I update a Subscription Plan "Test Plan":
      | Name       | Test Plan 2 |
      | Public     | True        |
      | Per Seat   | False       |
      | User Count | 10          |
      | Code Name  | test        |
    Then there should not be an error
    Then there should be a subscription plan called "Test Plan 2"

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 with:
      | Name       | Test Plan  |
      | Public     | True       |
      | Per Seat   | False      |
      | User Count | 10         |
      | Code Name  | test       |
      | Price      | 100        |
      | Currency   | USD        |
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 100          | USD            | week          | customer.one@example.org |
    When I update a Subscription Plan "Test Plan":
      | Name       | Test Plan 2 |
      | Public     | True        |
      | Per Seat   | False       |
      | User Count | 10          |
      | Code Name  | test        |
      | Price      | 120        |
      | Currency   | USD        |
    Then there should not be an error
    Then there should be a subscription plan called "Test Plan 2"