Feature: Customer List
  In order to keep track of customers
  As an API user
  I need to be see what customers are

  Scenario: Raw list
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the API to list customers
    Then I should see in the API response the customer "customer.one@example.org"
    And I should see in the API response the customer "customer.two@example.org"

  Scenario: Raw list
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the API to list customers with parameter "email" with value "one"
    Then I should see in the API response the customer "customer.one@example.org"
    Then I should not see in the API response the customer "customer.two@example.org"

  Scenario: No Results
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the API to list customers with parameter "email" with value "fifty"
    Then the API response data field should be empty
    Then I should not see in the API response the customer "customer.one@example.org"
    Then I should not see in the API response the customer "customer.two@example.org"

  Scenario: Country filter
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the API to list customers with parameter "country" with value "UK"
    Then I should see in the API response the customer "customer.two@example.org"
    Then I should not see in the API response the customer "customer.one@example.org"

  Scenario: Limited to one
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the API to list customers with parameter "limit" with value "1"
    Then I should see in the API response with only 1 result in the data set
    And the I should see in the API response there are more results

  Scenario: Pagination limited to one
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And I use the API to list customers with parameter "limit" with value "1"
    When I use the API to list customers with the last_key from the last response
    Then I should see in the API response with only 1 result in the data set
    And the I should not see in the API response there are more results

  Scenario: Reference filter
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the API to list customers with parameter "reference" with value "One"
    Then I should see in the API response with only 1 result in the data set
    Then I should see in the API response the customer "customer.one@example.org"
    Then I should not see in the API response the customer "customer.two@example.org"

  Scenario: External Reference filter
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the API to list customers with parameter "external_reference" with value "dfugfdu"
    Then I should see in the API response with only 1 result in the data set
    Then I should see in the API response the customer "customer.two@example.org"
    Then I should not see in the API response the customer "customer.one@example.org"