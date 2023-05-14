Feature: List Expiring Cards

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
      | customer.three@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following payment details:
      | Customer Email           | Last Four | Expiry Month | Expiry Year |
      | customer.one@example.org | 0444      | 03           | 25          |
      | customer.two@example.org | 0444      | 03           | 25          |
    And the following customers have cards that will expire this month:
      | Customer Email             | Last Four |
      | customer.one@example.org   | 0653      |
      | customer.three@example.org | 9434      |
    When I view the expiring cards page
    Then I will see there is an expiring card for "customer.one@example.org" with the last for 0653
    Then I will see there is an expiring card for "customer.three@example.org" with the last for 9434
    Then I will not see there is an expiring card for "customer.one@example.org" with the last for 0444