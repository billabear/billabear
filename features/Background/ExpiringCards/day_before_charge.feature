Feature: Day before expiring

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
      | customer.three@example.org | UK      | cust_dfugkjhu       | Customer Three |
      | customer.four@example.org | UK      | cust_rergkjhu       | Customer Four |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year |
      | customer.one@example.org | 0444      | 03           | 25          |
      | customer.two@example.org | 0444      | 03           | 25          |
    And the following customers have cards that will expire in 30 days:
      | Customer Email             | Last Four |
      | customer.one@example.org   | 0653      |
      | customer.three@example.org | 9434      |
    And the following customers have cards that will expired last month:
      | Customer Email             | Last Four |
      | customer.four@example.org  | 4949      |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Next Charge |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | +23 hours   |
      | Test Plan         | 3000         | USD            | month          | customer.four@example.org | +3 hours   |
      | Test Plan         | 3000         | USD            | month          | customer.three@example.org | +7 Days |
    And there are expiring card process for "customer.one@example.org" for card "0653" has sent the first email
    And there are expiring card process for "customer.three@example.org" for card "9434" has sent the first email
    And there are expiring card process for "customer.four@example.org" for card "4949" has sent the first email
    When the background task for day before next charge for expiring cards is ran
    Then the process for expired card for "customer.one@example.org" will be that a day before valid email was sent
    Then the process for expired card for "customer.three@example.org" will be that a day before valid email was not sent
    Then the process for expired card for "customer.four@example.org" will be that a day before no longer valid email was sent