Feature: View Lifetime Value

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Two  |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -17 months |
      | Test Plan         | 30000        | USD            | year          | customer.two@example.org |  -19 months |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -17 months |
    When I view the lifetime value:
    Then I should see a customer average lifespan
    And I should see a customer average lifetime value

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -17 months |
      | Test Plan         | 30000        | USD            | year          | customer.two@example.org |  -19 months |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -17 months |
    When I view the lifetime value:
      | Country | DE |
    Then I should see a customer average lifespan
    And I should see a customer average lifetime value

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -17 months |
      | Test Plan         | 30000        | USD            | year          | customer.two@example.org |  -19 months |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -17 months |
    When I view the lifetime value:
      | Payment Schedule | month |
    Then I should see a customer average lifespan
    And I should see a customer average lifetime value

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -17 months |
      | Test Plan         | 30000        | USD            | year          | customer.two@example.org |  -19 months |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -17 months |
    When I view the lifetime value:
      | Subscription Plan | Test Plan |
    Then I should see a customer average lifespan
    And I should see a customer average lifetime value

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -17 months |
      | Test Plan         | 30000        | USD            | year          | customer.two@example.org |  -19 months |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -17 months |
    When I view the lifetime value:
      | Brand | default |
    Then I should see a customer average lifespan
    And I should see a customer average lifetime value