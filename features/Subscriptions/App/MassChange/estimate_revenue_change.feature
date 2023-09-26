Feature: Create Subscription Mass Change

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
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 1000   | GBP      | true      | day     | true   |
      | Product One | 1100   | GBP      | true      | day     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 3400   | USD      | true      | month    | true   |
      | Product One | 3300   | USD      | true      | month    | true   |
      | Product One | 3400   | GBP      | true      | month    | true   |
      | Product One | 3500   | GBP      | true      | month    | true   |
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
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Third Plan |
      | Public     | True       |
      | Per Seat   | False      |
      | User Count | 10         |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   |
      | customer.three@example.org | UK      | cust_dfugfdu       | Customer Three |
      | customer.four@example.org  | UK      | cust_dfudsdu       | Customer Four  |
      | customer.five@example.org  | UK      | cust_dfugjfdu      | Customer Five  |
      | customer.six@example.org   | UK      | cust_dfugmnenf     | Customer Six   |
      | customer.seven@example.org | UK      | cust_dfurjg        | Customer Seven |
      | customer.eight@example.org | UK      | cust_drngu         | Customer Eight |
      | customer.nine@example.org  | UK      | cust_drmrdu        | Customer Nine  |
      | customer.ten@example.org   | UK      | cust_dloluesu      | Customer Ten   |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3000         | USD            | month          | customer.one@example.org |
      | Test Two          | 3000         | USD            | month          | customer.one@example.org |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org |
      | Test Plan         | 3000         | USD            | month          | customer.three@example.org |
      | Test Two          | 3000         | USD            | month          | customer.four@example.org |
      | Test Plan         | 3300         | USD            | month          | customer.five@example.org |
      | Test Plan         | 3300         | USD            | month          | customer.six@example.org |
      | Test Two          | 3300         | USD            | month          | customer.seven@example.org |
      | Test Plan         | 3300         | USD            | month          | customer.eight@example.org |
      | Test Plan         | 3400         | USD            | month          | customer.nine@example.org |
      | Test Two          | 3400         | GBP            | month          | customer.ten@example.org |
      | Third Plan        | 1000         | GBP            | day            | customer.ten@example.org |
# 5 at 3000 USD # 7 on Test Plan  # 1 Test Two at 3400 GBP  # 3 Test Plan at 3000 USD
# 4 at 3300 USD # 4 on Test Two   # 1 Test Two at 3300 USD  # 2 Test Two at 3000 USD
# 1 at 3400 USD #                 # 1 Test Plan at 3400 USD # 3 Test Plan at 3300 USD
# 1 at 3400 GBP #                 # 1 Test Plan at 3300 USD #

  Scenario: Plus 1200
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I estimate the new revenue for a mass subscription change:
      | Target Subscription Plan | Test Plan   |
      | Target Price Amount      | 3000        |
      | Target Price Currency    | USD         |
      | Target Price Schedule    | month       |
      | New Price Amount         | 3400        |
      | New Price Currency       | USD         |
      | New Price Schedule       | month       |
    Then I should be told the new revenue generated would be 1200 "USD"

  Scenario: Plus 2000
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I estimate the new revenue for a mass subscription change:
      | Target Price Amount      | 3000        |
      | Target Price Currency    | USD         |
      | Target Price Schedule    | month       |
      | New Price Amount         | 3400        |
      | New Price Currency       | USD         |
      | New Price Schedule       | month       |
    Then I should be told the new revenue generated would be 2000 "USD"

  Scenario: Minus 900
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I estimate the new revenue for a mass subscription change:
      | Target Subscription Plan | Test Plan   |
      | Target Price Amount      | 3300        |
      | Target Price Currency    | USD         |
      | Target Price Schedule    | month       |
      | New Price Amount         | 3000        |
      | New Price Currency       | USD         |
      | New Price Schedule       | month       |
    Then I should be told the new revenue generated would be "-900" "USD"

  Scenario: 3000
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I estimate the new revenue for a mass subscription change:
      | Target Subscription Plan | Third Plan |
      | Target Price Amount      | 1000       |
      | Target Price Currency    | GBP        |
      | Target Price Schedule    | day        |
      | New Price Amount         | 1100       |
      | New Price Currency       | GBP        |
      | New Price Schedule       | day        |
    Then I should be told the new revenue generated would be "3000" "GBP"

  Scenario: 0
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I estimate the new revenue for a mass subscription change:
      | Target Subscription Plan | Test Plan |
      | Target Price Amount      | 1000      |
      | Target Price Currency    | GBP       |
      | Target Price Schedule    | day       |
      | New Price Amount         | 1100      |
      | New Price Currency       | GBP       |
      | New Price Schedule       | day       |
    Then I should be told the new revenue generated would be "0" "GBP"