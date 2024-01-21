Feature: Edit country for tax purposes

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Edit Country
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
    When I the edit country for "United Kingdom" with:
      | Name      | United Kingdom |
      | ISO Code  | GB             |
      | Currency  | GBP            |
      | Threshold | 1000           |
    Then there will be a country called "United Kingdom" with the threshold 1000