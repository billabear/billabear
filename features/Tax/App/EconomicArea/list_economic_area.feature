Feature: List economic areas

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create country
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following economic areas exist:
      | Name                       | Threshold | Currency |
      | European Economic Area     | 1000000   | GBP      |
      | African Economic Community | 1000000   | GBP      |
    When I go to the economic areas list
    Then I should see an economic area called "European Economic Area"
