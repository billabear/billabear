Feature: Customer Update API
  In order to keep customer data up to date
  As an API user
  I need to be update a customer

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Get customer info
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I update the customer info via the APP for "customer.one@example.org" with:
      | Email              | customer.one@example.org |
      | Country            | GB                       |
      | External Reference | cust_4945959             |
      | Reference          | Test Customer            |
    Then the customer "customer.one@example.org" should have the reference "Test Customer"

