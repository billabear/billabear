Feature: User profile
  In order to get a referral bonus
  As a user
  I need to be able to be invite other people

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Invite user
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I invite "new.user@example.org"
    Then there will be an invite code for "new.user@example.org"

  Scenario: Invite user who is already a memeber
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I invite "sally.braun@example.org"
    Then there will not be an invite code for "sally.braun@example.org"

  Scenario: Invite user with role
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I invite "new.user@example.org" with role "ROLE_ADMIN"
    Then there will be an invite code for "new.user@example.org" with the role "ROLE_ADMIN"
