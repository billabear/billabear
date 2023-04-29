Feature: Read Notification Settings
  In order to know if the notification settings are correct
  As an APP user
  I need to be able to see the notification settings

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the notification settings are:
      | EMSP    | sendgrid       |
      | API Key | a-test-api-key |
    When I update the notification settings to:
      | EMSP                       | system              |
      | Outgoing Email             | example@example.org |
      | Send Customer Notification | false               |
    Then the notification settings for EMSP will be "system"