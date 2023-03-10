Feature: User login
  In order to use the system with my personal configuration
  As a user
  I need to be able to login

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  | Confirmed |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss | True      |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss | False     |
      | Sally Braun | sally.braun@example.org | AF@k3Pass | False     |

  Scenario: User does not exist
    When I have logged in as "sally.brown@example.org" with the password "ARealP@ss"
    Then I will see a login error

  Scenario: User does exist but with a different password
    When I have logged in as "sally.brown@example.org" with the password "ARealP@ss"
    Then I will see a login error

  Scenario: User does exist but is not confirmed
    When I have logged in as "tim.brown@example.org" with the password "AF@k3P@ss"
    Then I will see a login error

  Scenario: User does exist and correct password
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    Then I will be logged in
