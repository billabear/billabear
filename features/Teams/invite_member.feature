Feature: Send invites
  In order to allow all my employees to use the app
  As a shop owner
  I need to be able to invite employees to my team

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

  Scenario: Send invite
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I sent an invite to "tom.brown@example.org"
    Then there should be an invite for "tom.brown@example.org" to "Example"

  Scenario: View and see Team name
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I sent an invite to "tom.brown@example.org"
    When I sent an invite to "tom.brown@example.org"
    Then I should be told that the email has already been invited

  Scenario: View and see Team name
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I sent an invite to "tim.brown@example.org"
    Then there will not be an invite code for "tom.brown@example.org"

  Scenario: Cancel invite
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I sent an invite to "tom.brown@example.org"
    When I cancel the invite for "tom.brown@example.org"
    Then the invite for "tom.brown@example.org" shouldn't be usable