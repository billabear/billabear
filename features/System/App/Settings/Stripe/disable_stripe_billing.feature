Feature:

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  | Admin |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss | True  |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss | false |
      | Sally Braun | sally.braun@example.org | AF@k3Pass | false |

  Scenario: Disable stripe billing
    Given stripe billing is enabled
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I disable stripe billing
    Then there should be a stripe billing cancel task scheduled
    And stripe billing should be disabled


  Scenario: Disable stripe billing - not admin
    Given stripe billing is enabled
    Given I have logged in as "tim.brown@example.org" with the password "AF@k3P@ss"
    When I disable stripe billing
    Then stripe billing should be enabled

  Scenario: Enable stripe billing
    Given stripe billing is disabled
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I enable stripe billing
    Then stripe billing should be enabled

