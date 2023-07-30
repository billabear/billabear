Feature: Create the webhook

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Success
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a webhook with the following information:
      | Name | Test |
      | URL | https://example.org/webhook |
    Then there should be a webhook for the URL "https://example.org/webhook"

  Scenario: Failure no name
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a webhook with the following information:
      | Name |  |
      | URL | https://example.org/webhook |
    Then there should not be a webhook for the URL "https://example.org/webhook"
    And I should be told there is a validation error with the name

  Scenario: Failure invalid url
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a webhook with the following information:
      | Name | test |
      | URL | trest |
    Then I should be told there is a validation error with the URL