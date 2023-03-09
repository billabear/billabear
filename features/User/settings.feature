Feature: User Settings
  In order to keep my data up to date
  As a user
  I need to be able to be able to edit my settings

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

  Scenario: User logged in
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I edit my settings with the name "Test User 2"
    Then the user "sally.brown@example.org" will have the name "Test User 2"

  Scenario: User not logged in
    Given I am not logged in
    When I visit the settings page
    Then I will be on the login page
