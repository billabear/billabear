Feature: Make tax type

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Change default
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And there are the following tax types:
      | Name          | Default |
      | Digital Goods | True    |
      | Physical      | False   |
    When I make the tax type "Physical" default
    Then the tax type "Physical" is default
    Then the tax type "Digital Goods" is not default
