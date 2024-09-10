Feature: Tier Volume Pricing

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
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |

  Scenario:
    Given the follow price "Product One" for "USD" monthly price with tiered volume with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 11         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type          | Price Currency | Price Schedule | Customer                  | Next Charge | Status | Seats |
      | Test Plan         | tiered_volume | USD            | week           | customer.one@example.org  | +3 Minutes  | Active | 50    |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a month
    And there the latest invoice for "customer.one@example.org" will be for 1500 "USD"

  Scenario:
    Given the follow price "Product One" for "USD" monthly price with tiered volume with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 11         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type          | Price Currency | Price Schedule | Customer                  | Next Charge | Status | Seats |
      | Test Plan         | tiered_volume | USD            | week           | customer.one@example.org  | +3 Minutes  | Active | 9    |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a month
    And there the latest invoice for "customer.one@example.org" will be for 1000 "USD"

  Scenario:
    Given the follow price "Product One" for "USD" monthly price with tiered volume with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 1000       | 1000     |
      | 11         | -1        | 0          | 1500     |
    Given the following subscriptions exist:
      | Subscription Plan | Type          | Price Currency | Price Schedule | Customer                  | Next Charge | Status | Seats |
      | Test Plan         | tiered_volume | USD            | week           | customer.one@example.org  | +3 Minutes  | Active | 9    |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a month
    And there the latest invoice for "customer.one@example.org" will be for 10000 "USD"

  Scenario:
    Given the follow price "Product One" for "USD" monthly price with tiered volume with these tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 1000       | 1000     |
      | 11         | -1        | 200        | 0        |
    Given the following subscriptions exist:
      | Subscription Plan | Type          | Price Currency | Price Schedule | Customer                  | Next Charge | Status | Seats |
      | Test Plan         | tiered_volume | USD            | week           | customer.one@example.org  | +3 Minutes  | Active | 11    |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then the subscription for "customer.one@example.org" will expire in a month
    And there the latest invoice for "customer.one@example.org" will be for 2200 "USD"
