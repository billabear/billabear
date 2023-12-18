Feature: Edit Cancellation Request process

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
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org |

  Scenario: Fetch edit without dynamic transitions
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I go to the edit cancellation request workflow
    Then I will see the hardcoded cancellation request places
    And I will see the dynamic event handler for sending a webhook request

  Scenario: Fetch with dynamic transitions
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And there are workflow transitions
      | Name            | Priority | Workflow           | Handler | Handler Options                                   |
      | transition_name | 10       | cancel_subscription| webhook | {"method": "POST", "url": "https://example.org/"} |
    When I go to the edit cancellation request workflow
    Then I will see the transition "transition_name"

  Scenario: Create transition
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a workflow transition for "cancel_subscription" with:
      | Name | new_transition |
      | Priority | 143        |
      | Handler | webhook |
      | Handler Options |  {"method": "POST", "url": "https://example.org/"} |
    Then there should be a transition called "new_transition" for "cancel_subscription"