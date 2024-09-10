Feature: Metric Creation

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    Given the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |

  Scenario: Create a metric
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a metric via the app with the following:
      | Name               | Testing Metric |
      | Code               | testing_metric |
      | Type               | Resettable     |
      | Aggregation Method | Count          |
      | Ingestion          | Real Time      |
    Then there should be a metric called "Testing Metric"

  Scenario: Invalid no aggregation property
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a metric via the app with the following:
      | Name               | Testing Metric |
      | Code               | testing_metric |
      | Type               | Resettable     |
      | Aggregation Method | Unique Count   |
      | Ingestion          | Real Time      |
    Then there should not be a metric called "Testing Metric"

  Scenario: Valid with aggregation property
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a metric via the app with the following:
      | Name                 | Testing Metric      |
      | Code                 | testing_metric      |
      | Type                 | Resettable          |
      | Aggregation Method   | Unique Count        |
      | Aggregation Property | aggregationProperty |
      | Ingestion            | Real Time           |
    Then there should be a metric called "Testing Metric"

  Scenario: Valid with filters
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to have the following filters on a metric
      | Name       | Value     | Type      |
      | filter_one | value_one | inclusive |
      | filter_two | value_two | inclusive |
    When I create a metric via the app with the following:
      | Name                 | Testing Metric      |
      | Code                 | testing_metric      |
      | Type                 | Resettable          |
      | Aggregation Method   | Unique Count        |
      | Aggregation Property | aggregationProperty |
      | Ingestion            | Real Time           |
    Then there should be a metric called "Testing Metric"
    And the metric "Testing Metric" will have a filter for "filter_one"

  Scenario: Valid with invalid filters
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to have the following filters on a metric
      | Name       | Value     | Type      |
      | filter_one |           | exclusive |
      | filter_two | value_two | inclusive |
    When I create a metric via the app with the following:
      | Name                 | Testing Metric      |
      | Code                 | testing_metric      |
      | Type                 | Resettable          |
      | Aggregation Method   | Unique Count        |
      | Aggregation Property | aggregationProperty |
      | Ingestion            | Real Time           |
    Then there should not be a metric called "Testing Metric"
