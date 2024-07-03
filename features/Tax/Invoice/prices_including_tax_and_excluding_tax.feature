Feature: Prices include and exclude tax

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
      | Name           | ISO Code | Threshold | Currency |
      | United States  | US       | 0         | USD      |
      | Germany        | DE       | 0         | EUR      |
      | United Kingdom | GB       | 0         | GBP      |
    And the following country tax rules exist:
      | Country        | Tax Type      | Tax Rate | Valid From |
      | United States  | Digital Goods | 0        | -10 days   |
      | Germany        | Digital Goods | 20       | -10 days   |
      | United Kingdom | Digital Goods | 20       | -10 days   |
      | United States  | Physical      | 0        | -10 days   |
      | Germany        | Physical      | 20       | -10 days   |
      | United Kingdom | Physical      | 20       | -10 days   |
    And the follow products exist:
      | Name        | External Reference | Tax Type      |
      | Product One | prod_jf9j545       | Digital Goods |
      | Product Two | prod_jf9j542       | Physical      |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public | Include Tax |
      | Product One | 1000   | USD      | true      | week     | true   | false       |
      | Product One | 1000   | GBP      | true      | week     | true   | true        |
      | Product One | 3000   | USD      | true      | month    | true   | true       |
      | Product One | 30000  | USD      | true      | year     | false  | true       |
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
    Given a Subscription Plan exists for product "Product Two" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Two |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type | Payment Reference | Tax Number | Digital Tax Rate | Standard Tax Rate |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      | null              | FJDKSLfjdf | 10               | 15                |
      | customer.two@example.org   | GB      | cust_dfugfdu       | Customer Two   | card         | ref_valid         | ssdfds     |                  |                   |
      | customer.three@example.org | GB      | cust_mlklfdu       | Customer Three | card         | ref_valid         | gfdgsfd    |                  |                   |
      | customer.four@example.org  | GB      | cust_dkkoadu       | Customer Four  | card         | ref_fails         | 35435 43   |                  |                   |
      | customer.five@example.org  | GB      | cust_ddsjfu        | Customer Five  | card         | ref_valid         | dfadf      |                  |                   |
      | customer.six@example.org   | GB      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     |                  |                   |


  Scenario: Price excludes tax
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Active |
    And stripe billing is disabled
    And that the tax settings for tax customers with tax number is true
    When the background task to reinvoice active subscriptions
    And there will be an invoice for the amount of 1200 for "customer.four@example.org"

  Scenario: Price includes tax
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status |
      | Test Plan         | 1000         | GBP            | week           | customer.four@example.org  | +3 Minutes  | Active |
    And stripe billing is disabled
    And that the tax settings for tax customers with tax number is true
    When the background task to reinvoice active subscriptions
    And there will be an invoice for the amount of 1000 for "customer.four@example.org"
