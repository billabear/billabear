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


