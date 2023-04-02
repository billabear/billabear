Feature: Payment Details Delete
  In order to remove invalid payment details
  As an APP user
  I need to be able to delete payment details

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year | Name     |
      | customer.one@example.org | 0444      | 03           | 25          | Card One |
      | customer.one@example.org | 0444      | 03           | 25          | Card Two |

  Scenario: Get customer info
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I delete the payment details "Card One" for "customer.one@example.org" via APP
    Then the payment details "Card One" for "customer.one@example.org" should be deleted
