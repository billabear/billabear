Feature: Add Usage Limit

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Add Usage Limit
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I add customer usage limit to "customer.one@example.org":
      | Amount        | 3000 |
      | Warning Type  | Warn |
    Then there should be a limit to warn at 3000 for "customer.one@example.org"


  Scenario: Add Usage Limit
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
    When I add customer usage limit to "customer.one@example.org":
      | Amount        | -1 |
      | Warning Type  | Warn |
    Then there should not be a limit to warn at -1 for "customer.one@example.org"
