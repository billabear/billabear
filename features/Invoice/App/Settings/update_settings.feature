Feature: Update Invoice Settings

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Subsequential invoice generation
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the invoice number generation is random
    When I update the invoice number generation to subsequential with the count of 10
    Then the invoice number generation should be subsequential.
    Then the invoice subsequential number is 10

  Scenario: Random invoice generation
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the invoice number generation is subsequential
    When I update the invoice number generation to random
    Then the invoice number generation should be random.