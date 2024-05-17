Feature: Create Slack Notification

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following slack webhooks exist:
      | Name        | Webhook |
      | DevChat     | https://example.org |
      | Finance     | https://example.net |
    When I create a slack notification rule:
      | Webhook | DevChat          |
      | Event   | customer_created |
    Then there will be a slack notification rule for the webhook "DevChat" and event "customer_created"

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following slack webhooks exist:
      | Name        | Webhook             |
      | DevChat     | https://example.org |
      | Finance     | https://example.net |
    When I go create a slack notification
    Then I will see the slack webhook "DevChat" will be in the list
