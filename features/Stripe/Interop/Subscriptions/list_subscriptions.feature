Feature: Subscription List

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
      | Product One | 3500   | USD      | true      | month    | true   |
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
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org |
    When I fetch the subscription list from the stripe interopt layer
    Then I will see a subscription in the stripe interopt list for "Test Plan"

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org |
    When I fetch the subscription list from the stripe interopt layer for customer "customer.two@example.org"
    Then I will see a subscription in the stripe interopt list for "Test Plan"
    But I will not see a subscription in the stripe interopt list for "Test Two"

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org |
    When I fetch the subscription list from the stripe interopt layer for price 3000 "USD" "month"
    Then I will see 2 results in the stripe interopt list

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org |
    When I fetch the subscription list from the stripe interopt layer for price 3000 "USD" "month"
    Then I will see 2 results in the stripe interopt list

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    |
    When I fetch the subscription list from the stripe interopt layer for created at "-3 days"
    Then I will see 2 results in the stripe interopt list

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    |
    When I fetch the subscription list from the stripe interopt layer for created at greater than "-25 hours"
    Then I will see 1 results in the stripe interopt list

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    |
    When I fetch the subscription list from the stripe interopt layer for created at less than "-25 hours"
    Then I will see 2 results in the stripe interopt list

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Next Charge |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | +20 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | +21 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | +10 days    |
    When I fetch the subscription list from the stripe interopt layer for end of current period "+15 days"
    Then I will see 2 results in the stripe interopt list
    Then I will see a subscription in the stripe interopt list for "Test Plan"
    But I will not see a subscription in the stripe interopt list for "Test Two"


  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Next Charge |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | +20 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | +21 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | +10 days    |
    When I fetch the subscription list from the stripe interopt layer for end of current period less than "+15 days"
    Then I will see 1 results in the stripe interopt list
    Then I will not see a subscription in the stripe interopt list for "Test Plan"
    But I will see a subscription in the stripe interopt list for "Test Two"

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Started Current Period |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | +20 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | +21 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | +10 days    |
    When I fetch the subscription list from the stripe interopt layer for start of current period "+15 days"
    Then I will see 2 results in the stripe interopt list
    Then I will see a subscription in the stripe interopt list for "Test Plan"
    But I will not see a subscription in the stripe interopt list for "Test Two"


  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Started Current Period |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | +20 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | +21 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | +10 days    |
    When I fetch the subscription list from the stripe interopt layer for start of current period less than "+15 days"
    Then I will see 1 results in the stripe interopt list
    Then I will not see a subscription in the stripe interopt list for "Test Plan"
    But I will see a subscription in the stripe interopt list for "Test Two"

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | card         |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two | invoice      |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Started Current Period |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | +20 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | +21 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | +10 days    |
    When I fetch the subscription list from the stripe interopt layer for collection type "send_invoice"
    Then I will see 1 results in the stripe interopt list
    Then I will see a subscription in the stripe interopt list for "Test Plan"
    And I will not see a subscription in the stripe interopt list for "Test Two"


  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | card         |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two | invoice      |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Started Current Period |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | +20 days    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | +21 days    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | +10 days    |
    When I fetch the subscription list from the stripe interopt layer for collection type "charge_automatically"
    Then I will see 2 results in the stripe interopt list
    Then I will see a subscription in the stripe interopt list for "Test Plan"
    But I will see a subscription in the stripe interopt list for "Test Two"

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | card         |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two | invoice      |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Status    |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | Active    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | Active    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | Cancelled |
    When I fetch the subscription list from the stripe interopt layer for active subscriptions
    Then I will see 2 results in the stripe interopt list
    Then I will see a subscription in the stripe interopt list for "Test Plan"
    But I will not see a subscription in the stripe interopt list for "Test Two"

  Scenario:
    Given I have authenticated to the API
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    | Billing Type |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One | card         |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two | invoice      |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Started At | Status    |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org | -5 days    | Active    |
      | Test Plan         | 3500         | USD            | month          | customer.two@example.org | -2 days    | Active    |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org | -1 days    | Cancelled |
    When I fetch the subscription list from the stripe interopt layer for cancelled subscriptions
    Then I will see 1 results in the stripe interopt list
    Then I will not see a subscription in the stripe interopt list for "Test Plan"
    But I will see a subscription in the stripe interopt list for "Test Two"

