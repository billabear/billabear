Feature: Customer Read APP
  In order to manage payment details
  As an API user
  I need to be see customer's payment details

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
    Given I have authenticated to the API
    When I make the payment methods "Card One" for "customer.one@example.org" default
    Then the payment details "Card One" for "customer.one@example.org" should be default
    Then the payment details "Card Two" for "customer.one@example.org" should not be default
