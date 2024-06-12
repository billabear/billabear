Feature: Brands list
  In order to manage brands
  As an APP user
  I need to be see all the brands

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: List brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I go to the brand list
    Then I should see the brand "Default"

  Scenario: List brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    When I go to the brand list
    Then I should see the brand "Example"