Feature: Create Slack Webhook

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a slack webhook with:
      | Name    | A Test Webhook      |
      | Webhook | https://example.org |
    Then there will be a slack webhook called "A Test Webhook"

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following slack webhooks exist:
      | Name        | Webhook |
      | DevChat     | https://example.org |
    When I create a slack webhook with:
      | Name    | DevChat      |
      | Webhook | https://example.org |
    Then there should be an error for "name"
