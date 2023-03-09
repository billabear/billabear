Feature: User change password
  In order to keep my account secure
  As a user
  I need to be able to change my password

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

  Scenario: User has current password correct
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I change my password to "NewPassword" giving my current password as "AF@k3P@ss"
    Then the password "AF@k3P@ss" will not be valid for "sally.brown@example.org"
    And the password "NewPassword" will be valid for "sally.brown@example.org"

  Scenario: User does not have current password correct
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I change my password to "NewPassword" giving my current password as "ADifferentPassword"
    Then the password "AF@k3P@ss" will be valid for "sally.brown@example.org"
    And the password "NewPassword" will not be valid for "sally.brown@example.org"
