Feature: Customer Creation
  In order to keep track of customers
  As an APP user
  I need to be register a customer

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create customer
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a customer via the app with the following info
      | Email   | customer@example.org |
      | Country | DE                   |
    Then there should be a customer for "customer@example.org"

  Scenario: No email
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a customer via the app with the following info
      | Email   |    |
      | Country | DE |
    Then there should be an error for "email"
    And there should not be an error for "country"

  Scenario: No country
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a customer via the app with the following info
      | Email   | customer@example.org |
      | Country |                    |
    Then there should be an error for "country"
    And there should not be an error for "email"

  Scenario: Invalid email
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a customer with the following info
      | Email   | a-word   |
      | Country | DE |
    Then there should be an error for "email"
    And there should not be an error for "country"

  Scenario: Successfully create customer with references
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a customer via the app with the following info
      | Email              | customer@example.org |
      | Country            | DE                   |
      | External Reference | cust_4945959         |
      | Reference          | Test Customer        |
    Then there should be a customer for "customer@example.org"
    And the customer "customer@example.org" should have the external reference "cust_4945959"
    And the customer "customer@example.org" should have the reference "Test Customer"


  Scenario: Customer already exists
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer@example.org | DE      | cust_jf9j545       | Customer One |
    When I create a customer via the app with the following info
      | Email   | customer@example.org |
      | Country | DE                   |
    Then I should be told there is a conflict
