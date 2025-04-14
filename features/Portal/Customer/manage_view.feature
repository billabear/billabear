Feature: Manage Customer Endpoint

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         |

  Scenario: Valid token
    Given there is a manage customer session for "customer.one@example.org" that expires in "+5 minutes"
    When I view the manage customer endpoint for "customer.one@example.org"
    Then I will see the customer portal information

  Scenario: Expired token
    Given there is a manage customer session for "customer.one@example.org" that expires in "-5 minutes"
    When I view the manage customer endpoint for "customer.one@example.org"
    Then I will not see the customer portal information