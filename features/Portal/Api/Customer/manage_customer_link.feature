Feature: Fetch Customer Manage Link

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         |

  Scenario: Fetch Customer Manage Link
    Given I have authenticated to the API
    When I request the manage customer link for "customer.one@example.org"
    Then I will be given a token that is valid for managing the customer "customer.one@example.org"