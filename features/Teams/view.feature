Feature: View Team
  In order to manage my team in app
  As a shop owner
  I need to be able to see my team information

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

  Scenario: View and see invited members
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I sent an invite to "john.brown@example.org"
    When I view the team view
    Then I should see "john.brown@example.org" as an invited user

  Scenario: View and see invited members - limit hit
    Given I have logged in as "sally.braun@example.org" with the password "AF@k3Pass"
    And I sent an invite to "john.braun@example.org"
    When I view the team view
    Then I should not see "john.braun@example.org" as an invited user

  Scenario: View and see members
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I view the team view
    Then I should see the member "sally.brown@example.org" in the member list
    And I should see the member "tim.brown@example.org" in the member list