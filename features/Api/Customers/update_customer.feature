Feature: Customer Update API
  In order to keep customer data up to date
  As an API user
  I need to be update a customer


  Scenario: Get customer info
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    When I update the customer info via the API for "customer.one@example.org" with:
      | Email              | customer.one@example.org |
      | Country            | GB                       |
      | External Reference | cust_4945959             |
      | Reference          | Test Customer            |
    Then the customer "customer.one@example.org" should have the reference "Test Customer"

