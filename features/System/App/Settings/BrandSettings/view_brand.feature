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

  Scenario: View brands
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    When I go to view the brand "Example"
    Then I should see brand data for "Example"