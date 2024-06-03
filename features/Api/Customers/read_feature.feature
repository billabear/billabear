Feature: Customer Read API
  In order to manage a customer
  As an API user
  I need to be see what the customer info

  Scenario: Get customer info
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I view the customer info via the API for "customer.one@example.org"
    Then I will see the data "email" with value "customer.one@example.org"
