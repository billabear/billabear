Feature: Create Customer Usage Limit

  Scenario: Create customer usage limits
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    When I add customer usage limit via API to "customer.one@example.org":
      | Amount        | 3000    |
      | Warning Type  | Warning |
    Then there should be a limit to warn at 3000 for "customer.one@example.org"
