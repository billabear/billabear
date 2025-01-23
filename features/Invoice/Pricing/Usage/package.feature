Feature: Package Pricing usage

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
    And the following features exist:
      | Name        | Code        | Description     |
      | Feature One | feature_one | A dummy feature |
      | Feature Two | feature_two | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | invoice      |

  Scenario: Package Pricing
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Sum                |                      | Real Time |
    And the a price for "Product One" for 250 "USD" monthly price for package of 10 units for metric "Test One"
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status |
      | Test Plan         | package          | USD            | month          | customer.one@example.org | +3 Minutes  | Active |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated |
      | Test One | customer.one@example.org | Test Plan         | 1     | 99       |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a month
    And there the latest invoice for "customer.one@example.org" will be for 2500 "USD"
