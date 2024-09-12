Feature: Update metric counters

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

  Scenario: 30
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Sum                |                      | Real Time |
    Given the follow price "Product One" for "USD" monthly price with a usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 10         | 0        |
      | 21         | -1        | 5          | 0        |
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Started Current Period | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | month          | customer.one@example.org | -15 days               | +15 days    | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated | Created At |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       | -20 days   |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       | -10 days   |
    And stripe billing is disabled
    When the background task to update metric counters is ran
    Then the metric usage for "customer.one@example.org" and metric "Test One" will have the value 30
