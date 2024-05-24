Feature: Tax the correct state with threshold

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And that the following countries exist:
      | Name           | ISO Code | Threshold | Currency |
      | United Kingdom | GB       | 1770      | GBP      |
    And there are the following tax types:
      | Name             | Physical |
      | Digital Goods    | False    |
      | Digital Services | False    |
      | Physical         | True     |
    And the follow products exist:
      | Name        | External Reference | Tax Type      |
      | Product One | prod_jf9j545       | Digital Goods |
      | Product Two | prod_jf9j542       | Digital Goods |
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
      | Name       | Test Two |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type | Payment Reference | Tax Number | Digital Tax Rate | Standard Tax Rate | State  |
      | customer.seven@example.org | US      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     |                  |                   | Texas  |
      | customer.eight@example.org | CA      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     |                  |                   | quebec |

  Scenario:
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.seven@example.org | +3 Minutes  | Active |
      | Test Plan         | 1000         | USD            | week           | customer.eight@example.org | +3 Minutes  | Active |
    And that the following countries exist:
      | Name          | ISO Code | Threshold | Currency |
      | United States | US       | 0         | USD      |
      | Canada        | CA       | 0         | CAD      |
    And the following country tax rules exist:
      | Country        | Tax Type      | Tax Rate | Valid From |
      | United States  | Digital Goods | 0        | -10 days   |
      | Canada         | Digital Goods | 5       | -10 days   |
    And the following states exist:
      | Country       | Name       | Code | Threshold |
      | United States | Texas      | TX   | 10000     |
      | Canada        | Quebec     | QC   | 10000     |
    And the following state tax rules exist:
      | Country       | State  | Tax Rate | Tax Type      | Valid From |
      | United States | Texas  | 9        | Digital Goods | -10 days   |
      | Canada        | Quebec | 9.975    | Digital Goods | -10 days   |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    Then there the latest invoice for "customer.seven@example.org" will have tax country of "US"
    And there the latest invoice for "customer.seven@example.org" will have tax state of "Texas"
    Then there the latest invoice for "customer.seven@example.org" will have tax rate of "9"
    And there the latest invoice for "customer.eight@example.org" will have tax state of "Quebec"
    Then there the latest invoice for "customer.eight@example.org" will have tax rate of "14.975"
