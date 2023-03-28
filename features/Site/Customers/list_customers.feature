Feature: Customer List
  In order to keep track of customers
  As an APP user
  I need to be see what customers are

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Raw list
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the site to list customers
    Then I should see in the site response the customer "customer.one@example.org"
    And I should see in the site response the customer "customer.two@example.org"

  Scenario: Raw list
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the site to list customers with parameter "email" with value "one"
    Then I should see in the site response the customer "customer.one@example.org"
    Then I should not see in the site response the customer "customer.two@example.org"

  Scenario: No Results
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the site to list customers with parameter "email" with value "fifty"
    Then the site response data field should be empty
    Then I should not see in the site response the customer "customer.one@example.org"
    Then I should not see in the site response the customer "customer.two@example.org"

  Scenario: Country filter
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the site to list customers with parameter "country" with value "UK"
    Then I should see in the site response the customer "customer.two@example.org"
    Then I should not see in the site response the customer "customer.one@example.org"

  Scenario: Limited to one
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the site to list customers with parameter "per_page" with value "1"
    Then I should see in the site response with only 1 result in the data set
    And the I should see in the site response there are more results

  Scenario: Pagination limited to one
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And I use the site to list customers with parameter "per_page" with value "1"
    When I use the site to list customers with the last_key from the last response
    Then I should see in the site response with only 1 result in the data set
    And the I should not see in the site response there are more results

  Scenario: Reference filter
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the site to list customers with parameter "reference" with value "One"
    Then I should see in the site response with only 1 result in the data set
    Then I should see in the site response the customer "customer.one@example.org"
    Then I should not see in the site response the customer "customer.two@example.org"

  Scenario: External Reference filter
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    When I use the site to list customers with parameter "external_reference" with value "dfugfdu"
    Then I should see in the site response with only 1 result in the data set
    Then I should see in the site response the customer "customer.two@example.org"
    Then I should not see in the site response the customer "customer.one@example.org"