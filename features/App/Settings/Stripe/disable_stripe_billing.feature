Feature:

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Disable stripe billing
    Given stripe billing is enabled
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I disable stripe billing
    Then there should be a stripe billing cancel task scheduled
    And stripe billing should be disabled

  Scenario: Enable stripe billing
    Given stripe billing is disabled
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I enable stripe billing
    Then stripe billing should be enabled

