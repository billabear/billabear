Feature: List tax types

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create tax type
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And there are the following tax types:
      | Name     |
      | Digital Goods  |
      | Physical |
    When I go to the tax types list
    Then I will see a tax type in the list called "Digital Goods"
    And I will see a tax type in the list called "Physical"
