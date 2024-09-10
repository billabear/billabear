Feature: Continuous metric

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

  Scenario: Continuous metrics
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Continuous | Sum                |                      | Real Time |
    Given the follow price "Product One" for "USD" yearly price with a continuous usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 11         | -1        | 1          | 0        |
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | week           | customer.one@example.org | +3 Minutes  | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated | Properties | Created At |
      | Test One | customer.one@example.org | Test Plan         | 1     | 10       | []         | -1 hour    |
    Given the following invoices exist:
      | Customer                 | Created At |
      | customer.one@example.org | -1 year    |
    And the last invoice for "customer.one@example.org" had a metric usage for "Test One" that was 15
    And the metric usage for "customer.one@example.org" and metric "Test One" that has the value 25
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a year
    And there the latest invoice for "customer.one@example.org" will be for 10 "USD"
    And the metric usage for "customer.one@example.org" and metric "Test One" will have the value 25
