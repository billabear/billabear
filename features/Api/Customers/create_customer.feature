Feature: Customer Creation
  In order to keep track of customers
  As an API user
  I need to be register a customer

  Scenario: Successfully create customer
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email   | customer@example.org |
      | Country | DE                   |
    Then there should be a customer for "customer@example.org"

  Scenario: No email
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email   |    |
      | Country | DE |
    Then there should be an error for "email"
    And there should not be an error for "country"

  Scenario: No country
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email   | customer@example.org |
      | Country |                    |
    Then there should be an error for "country"
    And there should not be an error for "email"

  Scenario: Invalid email
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email   | a-word   |
      | Country | DE |
    Then there should be an error for "email"
    And there should not be an error for "country"

  Scenario: Successfully create customer with references
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email              | customer@example.org |
      | Country            | DE                   |
      | External Reference | cust_4945959         |
      | Reference          | Test Customer        |
    Then there should be a customer for "customer@example.org"
    And the customer "customer@example.org" should have the external reference "cust_4945959"
    And the customer "customer@example.org" should have the reference "Test Customer"


  Scenario: Customer already exists
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer@example.org | DE      | cust_jf9j545       | Customer One |
    When I create a customer with the following info
      | Email   | customer@example.org |
      | Country | DE                   |
    Then I should be told there is a conflict
