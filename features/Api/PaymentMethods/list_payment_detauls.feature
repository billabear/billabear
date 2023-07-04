Feature: List Payment Details

  Background:
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Add Card |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | False    |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two | false    |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year | Name     |
      | customer.one@example.org | 0444      | 03           | 25          | Card One |
      | customer.one@example.org | 0445      | 03           | 25          | Card Two |
      | customer.two@example.org | 0446      | 03           | 25          | Card Two |

  Scenario: Get customer info
    Given I have authenticated to the API
    When I fetch the payment details via API for customer "customer.one@example.org"
    Then the response should have 2 items in the data array
    And the response should contain the payment details for last four "0444"
    And the response should contain the payment details for last four "0445"
    But the response should not contain the payment details for last four "0446"
