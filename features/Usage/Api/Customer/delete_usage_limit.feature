Feature: Delete Usage Limit

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Delete Usage Limit
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    And there should be a usage limits for "customer.one@example.org":
      | Amount | Warning Type |
      | 1000   | Warn         |
      | 3000   | Warn         |
    When I delete the usage limit via the API for 1000 for "customer.one@example.org"
    Then there should be a limit to warn at 3000 for "customer.one@example.org"
    Then there should not be a limit to warn at 1000 for "customer.one@example.org"
