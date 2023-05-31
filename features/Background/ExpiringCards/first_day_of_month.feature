Feature: List Expiring Cards

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
      | Name       | Test Two  |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |

  Scenario: Send emails
    Given the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
      | customer.three@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year |
      | customer.one@example.org | 0444      | 03           | 25          |
      | customer.two@example.org | 0444      | 03           | 25          |
    And the following customers have cards that will expire this month:
      | Customer Email             | Last Four |
      | customer.one@example.org   | 0653      |
      | customer.three@example.org | 9434      |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
    When the background task for sending first card expiry warning
    Then the workflow for expired card will exist for "customer.one@example.org"
    And the workflow for expired card will not exist for "customer.two@example.org"
    And the workflow for expired card will not exist for "customer.three@example.org"
    Then the workflow for expired card for "customer.one@example.org" will be that an email had been sent