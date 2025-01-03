Feature: Create invoice

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
      | Public     | True     |
      | Per Seat   | True    |
      | User Count | 10       |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type | Add Card |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |          |
      | customer.two@example.org   | UK      | cust_dfugfdu       | Customer Two   | card         | true     |
      | customer.three@example.org | UK      | cust_mlklfdu       | Customer Three | card         | true     |
      | customer.four@example.org  | UK      | cust_dkkoadu       | Customer Four  | card         | true     |
      | customer.five@example.org  | UK      | cust_ddsjfu        | Customer Five  | card         | false    |
      | customer.six@example.org   | UK      | cust_jliujoi       | Customer Six   | card         | true     |
      | customer.seven@example.org | UK      | cust_jliujoi       | Customer Six   | invoice      |          |
    And there are the following tax types:
      | Name     |
      | Digital Goods |
      | Physical |

  Scenario: Create subscriptions
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to invoice the customer "customer.seven@example.org"
    And I want to invoice for a subscription to "Test Two" at 1000 in "USD" per "week"
    And I want to invoice for a subscription to "Test Plan" at 2000 in "USD" per "week"
    When I finalise the invoice in APP
    Then there will be an unpaid invoice for "customer.seven@example.org"
    And the latest invoice for "customer.seven@example.org" will have amount due as 3000

  Scenario: Create subscriptions - per seat
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to invoice the customer "customer.seven@example.org"
    And I want to invoice for a subscription to "Per Seat Plan" at 3000 in "USD" per "week" with 30 seats
    When I finalise the invoice in APP
    Then there will be an unpaid invoice for "customer.seven@example.org"
    And the latest invoice for "customer.seven@example.org" will have amount due as 90000

  Scenario: Create subscriptions and one-off item
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to invoice the customer "customer.seven@example.org"
    And I want to invoice for a subscription to "Test Two" at 1000 in "USD" per "week"
    And I want to invoice for a subscription to "Test Plan" at 2000 in "USD" per "week"
    And I want to invoice for a bespoke one-off fee for "Setup Costs" at 5000 in "USD" including tax for a digital goods
    When I finalise the invoice in APP
    Then there will be an unpaid invoice for "customer.seven@example.org"
    And the latest invoice for "customer.seven@example.org" will have amount due as 8000
    Then there should be a subscription for the user "customer.seven@example.org"

  Scenario: Create without subscriptions and one off item
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to invoice the customer "customer.seven@example.org"
    And I want to invoice for a bespoke one-off fee for "Setup Costs" at 5000 in "USD" including tax for a digital goods
    When I finalise the invoice in APP
    Then there will be an unpaid invoice for "customer.seven@example.org"
    And the latest invoice for "customer.seven@example.org" will have amount due as 5000

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to invoice the customer "customer.seven@example.org"
    And I want to invoice for a subscription to "Test Two" at 1000 in "USD" per "week"
    And I want to invoice for a subscription to "Test Three" at 3000 in "USD" per "month"
    When I finalise the invoice in APP
    Then I will get the error that the payment schedules must all match


  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to invoice the customer "customer.seven@example.org"
    And I want to invoice for a subscription to "Test Two" at 1000 in "USD" per "week"
    And I want to invoice for a subscription to "Test Plan" at 2000 in "USD" per "week"
    And I want the invoice to be paid within "60 days"
    When I finalise the invoice in APP
    And the latest invoice for "customer.seven@example.org" will be due in "60 days"

  Scenario: Customer has no card
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I want to invoice the customer "customer.five@example.org"
    And I want to invoice for a subscription to "Test Two" at 1000 in "USD" per "week"
    And I want to invoice for a subscription to "Test Plan" at 2000 in "USD" per "week"
    And I want the invoice to be paid within "60 days"
    When I finalise the invoice in APP
    And the latest invoice for "customer.five@example.org" will be due in "60 days"
