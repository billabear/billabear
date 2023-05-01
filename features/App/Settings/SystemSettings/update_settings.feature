Feature: Update system Settings
  In order to keep the system settings correct
  As an APP user
  I need to be able to update the system settings

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create email template
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the system settings are:
      | Webhook URL    | https://webhook.example.org       |
      | Timezone | Europe/Berlin                   |
    When I update the system settings to:
      | Webhook URL    | https://webhook.example.com       |
      | Timezone | Europe/Berlin                   |
    Then the system settings for webhook url will be "https://webhook.example.com"