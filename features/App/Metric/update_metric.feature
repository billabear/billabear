Feature: Update Metric

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

  Scenario: Update a metric
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Count              |                      | Real Time |
      | Test Two | test_two | Resettable | Unique Count       | propertyName         | Hourly    |
    When I update the metric "Test One" via the app with the following:
      | Name               | Testing Metric |
      | Type               | Resettable     |
      | Aggregation Method | Count          |
      | Ingestion          | Real Time      |
    Then there should not be a metric called "Test One"
    And there should be a metric called "Testing Metric"
