Feature: Add tax type

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create tax type
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a tax type with:
      | Name     | Digital Services |
      | Physical | False            |
    Then there will be a tax type with the name "Digital Services"