Feature: User request password reset
  In order to use the system with my personal configuration when I forget my password
  As a user
  I need to be able to reset my password

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

  Scenario: Confirmed User with valid code
    Given a password reset code "code" for "sally.brown@example.org" exists
    When I reset my password to "NewPassword" with the code "code" for "sally.brown@example.org"
    Then there will be a new password for "sally.brown@example.org"

  Scenario: Confirmed User with valid code but expired
    And a password reset code "code" for "sally.brown@example.org" exists
    And the password reset code "code" has expired
    When I reset my password to "NewPassword" with the code "code" for "sally.brown@example.org"
    Then there will not be a new password for "sally.brown@example.org"

  Scenario: Confirmed User with valid code but already been used
    And a password reset code "code" for "sally.brown@example.org" exists
    And the password reset code "code" has already been used
    When I reset my password to "NewPassword" with the code "code" for "sally.brown@example.org"
    Then there will not be a new password for "sally.brown@example.org"

  Scenario: Confirmed User with invalid code
    And a password reset code "code" for "sally.brown@example.org" exists
    When I reset my password to "NewPassword" with the code "a different code" for "sally.brown@example.org"
    Then there will not be a new password for "sally.brown@example.org"

  Scenario: Unconfirmed User with valid code
    And a password reset code "code" for "sally.brown@example.org" exists
    When I reset my password to "NewPassword" with the code "code" for "sally.brown@example.org"
    Then there will be a new password for "sally.brown@example.org"
    And the user "sally.brown@example.org" will be confirmed