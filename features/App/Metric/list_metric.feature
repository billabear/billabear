Feature: Metric List

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

  Scenario: List metric
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow metrics exist:
      | Name     | Code     | Type       | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Resettable | Count              |                      | Real Time |
      | Test Two | test_two | Continuous | Unique Count       | propertyName         | Hourly    |
    When I go to the metric list page in the app
    Then I will see 2 items in the list
    And I will see a metric with the name "Test One"
    And I will see a metric with the name "Test Two"
