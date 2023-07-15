Feature: Set tax rate on customers

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         |
      | customer.three@example.org | UK      | cust_mlklfdu       | Customer Three | card         |
      | customer.four@example.org  | UK      | cust_dkkoadu       | Customer Four  | card         |
      | customer.five@example.org  | UK      | cust_ddsjfu        | Customer Five  | card         |
      | customer.six@example.org   | UK      | cust_jliujoi       | Customer Six   | card         |

  Scenario: Update customer tax rates
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I update the customer info via the APP for "customer.one@example.org" with:
      | Email              | customer.one@example.org |
      | Country            | GB                       |
      | External Reference | cust_4945959             |
      | Reference          | Test Customer            |
      | Digital Tax Rate   | 12.4                     |
      | Standard Tax Rate  | 20.0                     |
    Then the customer "customer.one@example.org" should have the digital tax rate 12.4
    And the customer "customer.one@example.org" should have the standard tax rate 20.0


