Feature: Payment Details Delete
  In order to remove invalid payment details
  As an API user
  I need to be able to delete payment details

  Background:
    Given the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year | Name     |
      | customer.one@example.org | 0444      | 03           | 25          | Card One |
      | customer.one@example.org | 0444      | 03           | 25          | Card Two |

  Scenario: Get customer info
    Given I have authenticated to the API
    When I delete the payment methods "Card One" for "customer.one@example.org"
    Then the payment details "Card One" for "customer.one@example.org" should be deleted
    Then the payment details "Card Two" for "customer.one@example.org" should not be deleted
