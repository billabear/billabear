Feature:

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  | Admin |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss | True  |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss | false |
      | Sally Braun | sally.braun@example.org | AF@k3Pass | false |

  Scenario: Register hook
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I register the webhook url "https://example.org/webhook"
    Then the system settings will have a webhook id

  Scenario: Deregister hook
    Given the webhook url is set for "https://example.org/webhook"
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I deregister my webhook
    Then the system settings for webhook will be nullified
    And the system settings will not have a webhook id