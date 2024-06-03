Feature: View checkout

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And there are the following tax types:
      | Name             | Physical |
      | Digital Goods    | False    |
      | Digital Services | False    |
      | Physical         | True     |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency | In EU |
      | United Kingdom | GB       | 1770      | GBP      | False |
      | United States  | US       | 0         | USD      | False |
      | Germany        | DE       | 0         | EUR      | True  |
    And the following country tax rules exist:
      | Country        | Tax Type      | Tax Rate | Valid From |
      | United States  | Digital Goods | 17.5     | -10 days   |
      | United Kingdom | Digital Goods | 17.5     | -10 days   |
      | Germany        | Digital Goods | 17.5     | -10 days   |
    And the follow products exist:
      | Name        | External Reference | Tax Type      |
      | Product One | prod_jf9j545       | Digital Goods |
      | Product Two | prod_jf9j542       | Digital Goods |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 2000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | week     | false  |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 1000 in "USD" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 2000 in "USD" with:
      | Name       | Test Two |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 3000 in "USD" with:
      | Name       | Test Three |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 3000 in "USD" with:
      | Name       | Per Seat Plan |
      | Public     | True          |
      | Per Seat   | True          |
      | User Count | 10             |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         |
      | customer.three@example.org | UK      | cust_mlklfdu       | Customer Three | card         |
      | customer.four@example.org  | UK      | cust_dkkoadu       | Customer Four  | card         |
      | customer.five@example.org  | UK      | cust_ddsjfu        | Customer Five  | card         |
      | customer.six@example.org   | UK      | cust_jliujoi       | Customer Six   | card         |
      | customer.seven@example.org | UK      | cust_jliujoi       | Customer Six   | invoice      |

  Scenario: View Checkout
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And a permanent checkout called "Test" exists in "USD":
      | Description | Total | Sub Total | Vat Total |
      | Setup costs | 12000 | 8000      | 4000      |
    When I submit the customer in the portal checkout for "Test"
      | Email              | customer@example.org |
      | Country            | DE                   |
    Then there should be a customer for "customer@example.org"
    And the response should have the stripe config
    And there should be a checkout session for "customer@example.org"
    And there should have an updated tax amount in the response of 4000
