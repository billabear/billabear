Feature: Customer Creation event

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create customer
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following webhook endpoints exist:
      | Name        | URL                 |
      | Example.org | https://example.org |
    When I create a customer via the app with the following info
      | Email   | customer@example.org |
      | Country | DE                   |
    Then there should be a customer for "customer@example.org"
    Then there should be a webhook event for customer created

  Scenario: No email
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following webhook endpoints exist:
      | Name        | URL                 |
      | Example.org | https://example.org |
    When I create a customer via the app with the following info
      | Email   |    |
      | Country | DE |
    Then there should not be a webhook event for customer created