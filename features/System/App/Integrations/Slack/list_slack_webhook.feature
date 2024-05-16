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
      | Name        | Webhook |
      | DevChat     | https://example.org |
      | Finance     | https://example.net |
    When I go to the slack webhook list page
    Then I will see a webhook for "DevChat"
    And I will see a webhook for "Finance"
