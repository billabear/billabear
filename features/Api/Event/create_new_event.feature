Feature: Create Event

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
      | Per Seat   | True      |

  Scenario: Add Event
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Seats |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | 3     |
    And the follow metrics exist:
      | Name               | Code     | Type      | Aggregation Method | Aggregation Property | Ingestion |
      | Test One           | test_one | Metered   | Count              |                      | Real Time |
    When I create an event for customer "customer.one@example.org" for subscription for "Test Plan" and metric "Test One" with the value 1
    Then there should be an event for customer "customer.one@example.org"

  Scenario: Add Event with properties
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Seats |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | 3     |
    And the follow metrics exist:
      | Name               | Code     | Type      | Aggregation Method | Aggregation Property | Ingestion |
      | Test One           | test_one | Metered   | Count              |                      | Real Time |
    When I create an event with properties for customer "customer.one@example.org" for subscription for "Test Plan" and metric "Test One" with the value 1
    Then there should be an event for customer "customer.one@example.org"
