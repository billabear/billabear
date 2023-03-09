Feature: User profile
  In order to get a referral bonus
  As a user
  I need to be able to be invite other people

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

  Scenario: Invite user
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I invite "new.user@example.org"
    Then there will be an invite code for "new.user@example.org"

  Scenario: Invite user who is already a memeber
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I invite "sally.braun@example.org"
    Then there will not be an invite code for "sally.braun@example.org"
