Feature: Create Slack Webhook

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following slack webhooks exist:
      | Name        | Webhook             | Enabled |
      | DevChat     | https://example.org | True    |
      | Finance     | https://example.net | True    |
    When I disable the "DevChat" slack webhook
    Then the "DevChat" slack webhook is not enabled
    Then the "Finance" slack webhook is enabled

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following slack webhooks exist:
      | Name        | Webhook             | Enabled |
      | DevChat     | https://example.org | False    |
      | Finance     | https://example.net | True    |
    When I enable the "DevChat" slack webhook
    Then the "DevChat" slack webhook is enabled
    Then the "Finance" slack webhook is enabled
