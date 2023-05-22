Feature: Create Credit

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   |
      | customer.three@example.org | UK      | cust_dljkjng       | Customer three |

  Scenario: Create credit
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a credit for "customer.one@example.org" for 1000 in the currency "USD"
    Then there should be a credit for "customer.one@example.org" for 1000 in the currency "USD"
    And there should be a credit created by "sally.brown@example.org"

  Scenario: Create debit
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following credit transactions exist:
      | Customer                 | Type   | Amount | Currency |
      | customer.one@example.org | credit | 1000   | USD      |
    When I create a debit for "customer.one@example.org" for 1000 in the currency "USD"
    Then the credit amount for "customer.one@example.org" should be 0