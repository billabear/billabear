Feature: List Slack Notification

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following slack webhooks exist:
      | Name        | Webhook             |
      | DevChat     | https://example.org |
      | Finance     | https://example.net |
    And the following slack notifications exist:
      | Webhook | Event             |
      | DevChat | customer_created  |
      | Finance | payment_processed |
    When I go to the slack notification list page
    Then I should see a slack notification in the list for "DevChat" and event "customer_created"
    Then I should see a slack notification in the list for "Finance" and event "payment_processed"
