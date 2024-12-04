Feature: Read Customer Usage Limit

  Scenario: Get customer usage limits
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    And there should be a usage limits for "customer.one@example.org":
      | Amount | Warning Type |
      | 1000   | Warn         |
      | 3000   | Warn         |
    When I request the usage limits for customer "customer.one@example.org"
    Then there should be a usage limits list response should include a warn level limit for 1000
    Then there should be a usage limits list response should include a warn level limit for 3000
