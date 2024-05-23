Feature: Add economic areas

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create economic area
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create an economic area with the following data:
      | Name      | European Economic Area |
      | Currency  | GBP                    |
      | Threshold | 1000000                |
    Then there will be an economic area called "European Economic Area"


  Scenario: Unique name error
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following economic areas exist:
      | Name                   | Threshold | Currency |
      | European Economic Area | 1000000   | GBP      |
    When I create an economic area with the following data:
      | Name      | European Economic Area |
      | Currency  | EUR                    |
      | Threshold | 1000000                |
    Then there should be an error for "name"
