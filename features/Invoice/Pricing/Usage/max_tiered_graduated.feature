Feature: Sum Tier Graduated Pricing usage

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

  Scenario: Max usage
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Max                | hour                 | Real Time |
    Given the follow price "Product One" for "USD" monthly price with a usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 0          | 1000     |
      | 21         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | month          | customer.one@example.org | +3 Minutes  | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated | Properties                |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       | {"hour": 12, "minute": 9} |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       | {"hour": 25, "minute": 4} |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a month
    And there the latest invoice for "customer.one@example.org" will be for 2500 "USD"

  Scenario: Max usage
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Max                | hour                 | Real Time |
    Given the follow price "Product One" for "USD" yearly price with a usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 0          | 1000     |
      | 21         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | year           | customer.one@example.org | +3 Minutes  | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated | Created At | Properties                |
      | Test One | customer.one@example.org | Test Plan         | 1     | 5        | -5 months  | {"hour": 16, "minute": 4} |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a year
    And there the latest invoice for "customer.one@example.org" will be for 1000 "USD"

  Scenario: Max usage
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Max                | hour                 | Real Time |
    Given the follow price "Product One" for "USD" weekly price with a usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 0          | 1000     |
      | 21         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | week           | customer.one@example.org | +3 Minutes  | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated | Created At | Properties                |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       | -5 years   | {"hour": 26, "minute": 4} |
      | Test One | customer.one@example.org | Test Plan         | 1     | 5        | -5 months  | {"hour": 46, "minute": 4} |
      | Test One | customer.one@example.org | Test Plan         | 1     | 5        | -3 days    | {"hour": 16, "minute": 4} |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And there the latest invoice for "customer.one@example.org" will be for 1000 "USD"

  Scenario: Max usage
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion | Filters                                              |
      | Test One | test_one | Resettable | Max                | hour                 | Real Time | {"colour": {"value": "yellow", "type": "inclusive"}} |
    Given the follow price "Product One" for "USD" weekly price with a usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 0          | 1000     |
      | 21         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | week           | customer.one@example.org | +3 Minutes  | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated | Created At | Properties                                    |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       | -5 years   | {}                                            |
      | Test One | customer.one@example.org | Test Plan         | 1     | 5        | -5 months  | {}                                            |
      | Test One | customer.one@example.org | Test Plan         | 1     | 5        | -3 days    | {"colour": "yellow", "hour": 16, "minute": 4} |
      | Test One | customer.one@example.org | Test Plan         | 1     | 16       | -3 days    | {"colour": "blue", "hour": 26, "minute": 4}   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And there the latest invoice for "customer.one@example.org" will be for 1000 "USD"

  Scenario: Max usage
    Given the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion | Filters                                              |
      | Test One | test_one | Resettable | Max                | hour                 | Real Time | {"colour": {"value": "yellow", "type": "exclusive"}} |
    Given the follow price "Product One" for "USD" weekly price with a usage tiered graduated for metric "Test One" with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 20        | 0          | 1000     |
      | 21         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type             | Price Currency | Price Schedule | Customer                 | Next Charge | Status | Seats |
      | Test Plan         | tiered_graduated | USD            | week           | customer.one@example.org | +3 Minutes  | Active | 5     |
    And the following events exist:
      | Metric   | Customer                 | Subscription Plan | Value | Repeated | Created At | Properties                                    |
      | Test One | customer.one@example.org | Test Plan         | 2     | 15       | -5 years   | {}                                            |
      | Test One | customer.one@example.org | Test Plan         | 1     | 5        | -5 months  | {}                                            |
      | Test One | customer.one@example.org | Test Plan         | 1     | 5        | -3 days    | {"colour": "yellow", "hour": 16, "minute": 4} |
      | Test One | customer.one@example.org | Test Plan         | 2     | 16       | -3 days    | {"colour": "blue", "hour": 26, "minute": 4}   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a week
    And there the latest invoice for "customer.one@example.org" will be for 2500 "USD"
