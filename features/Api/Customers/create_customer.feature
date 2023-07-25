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

  Scenario: Successfully create customer with references
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email              | customer@example.org |
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
    Then I should not be told there is a conflict

  Scenario: Successfully create customer with references and billing type
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email              | customer@example.org |
      | Country            | DE                   |
      | External Reference | cust_4945959         |
      | Reference          | Test Customer        |
      | Billing Type       | invoice              |
    Then there should be a customer for "customer@example.org"
    And the customer "customer@example.org" should have the external reference "cust_4945959"
    And the customer "customer@example.org" should have the reference "Test Customer"
    And the customer "customer@example.org" should have the billing type "invoice"


  Scenario: Successfully create customer with references and tax number
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email              | customer@example.org |
      | Country            | DE                   |
      | External Reference | cust_4945959         |
      | Reference          | Test Customer        |
      | Billing Type       | invoice              |
      | Tax Number         | GB2494944            |
    Then there should be a customer for "customer@example.org"
    And the customer "customer@example.org" should have the tax number "GB2494944"


  Scenario: Successfully create customer as business customer
    Given the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email              | customer@example.org |
      | Country            | DE                   |
      | External Reference | cust_4945959         |
      | Reference          | Test Customer        |
      | Billing Type       | invoice              |
      | Brand              | example              |
      | Locale             | en                   |
      | Tax Number         | GB2494944            |
      | Type               | Business             |
    Then there should be a customer for "customer@example.org"
    And the customer "customer@example.org" should be a business customer

  Scenario: Successfully create customer as Individual customer
    Given the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    Given I have authenticated to the API
    When I create a customer with the following info
      | Email              | customer@example.org |
      | Country            | DE                   |
      | External Reference | cust_4945959         |
      | Reference          | Test Customer        |
      | Billing Type       | invoice              |
      | Brand              | example              |
      | Locale             | en                   |
      | Tax Number         | GB2494944            |
      | Type               | Individual           |
    Then there should be a customer for "customer@example.org"
    And the customer "customer@example.org" should be a individual customer