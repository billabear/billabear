Feature: View Plans
  In order to choose a Plan
  A Customer
  I need to be able to see the Plans

  Background:
    Given the following teams exist:
      | Name    | Plan     |
      | Example | Standard |
      | Second  | Basic    |
    Given the following accounts exist:
      | Name        | Email                   | Password  | Team    |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss | Example |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss | Example |
      | Sally Braun | sally.braun@example.org | AF@k3Pass | Second  |

  Scenario: View Plans
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I view the plans
    Then I should see the plans that are configured