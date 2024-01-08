Feature: Add country for tax purposes

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create product
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a country with the following data:
      | Name     | United Kingdom |
      | ISO Code | GB             |
    Then there will be a country called "United Kingdom" with the ISO Code "GB"