Feature: Handle One stop shop

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And the follow brands exist:
      | Name    | Code                    | Email               | Country |
      | Example | example                 | example@example.org | DE      |
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
      | France         | FR       | 0         | EUR      | True  |
    And the following country tax rules exist:
      | Country        | Tax Type      | Tax Rate | Valid From |
      | France         | Digital Goods | 17.5     | -10 days   |
      | United States  | Digital Goods | 17.5     | -10 days   |
      | United Kingdom | Digital Goods | 17.5     | -10 days   |
      | Germany        | Digital Goods | 17.5     | -10 days   |
      | Germany        | Physical      | 10.5     | -10 days   |
    And the follow products exist:
      | Name        | External Reference | Tax Type      | Physical |
      | Product One | prod_jf9j545       | Physical      | True     |
      | Product Two | prod_jf9j542       | Digital Goods | False    |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product Two | 1001   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    And the follow products exist:
      | Name        | External Reference | Tax Type      | Physical |
      | Product One | prod_jf9j545       | Physical      | True     |
      | Product Two | prod_jf9j542       | Digital Goods | False    |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product Two | 1001   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price "Price One" with:
      | Name       | Test Plan |
      | Public     | True      |
      | Per Seat   | False     |
      | User Count | 10        |
    And the follow customers exist:
      | Email                      | Country | External Reference | Reference      | Billing Type | Payment Reference | Tax Number | Type       | Brand   |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      | null              | FJDKSLfjdf | Business   | example |
      | customer.two@example.org   | FR      | cust_dfugfdu       | Customer Two   | card         | ref_valid         | ssdfds     | Individual | example |
      | customer.three@example.org | CZ      | cust_mlklfdu       | Customer Three | card         | ref_valid         |            | Business   | example |
      | customer.four@example.org  | GB      | cust_dkkoadu       | Customer Four  | card         | ref_fails         | 35435 43   | Individual | example |
      | customer.five@example.org  | GB      | cust_ddsjfu        | Customer Five  | card         | ref_valid         | dfadf      | Individual | example |
      | customer.six@example.org   | GB      | cust_jliujoi       | Customer Six   | card         | ref_fails         | fdsafd     | Individual | example |
      | customer.seven@example.org | US      | cust_mlklfdu       | Customer Three | card         | ref_valid         |            | Business   | example |

  Scenario:
    Given the EU one stop shop rule is enabled
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.two@example.org | +3 Minutes  | Active |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.two@example.org" will have tax country of "FR"

  Scenario:
    Given the EU one stop shop rule is not enabled
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 | Next Charge | Status |
      | Test Plan         | 1000         | USD            | week           | customer.three@example.org | +3 Minutes  | Active |
    And stripe billing is disabled
    When the background task to reinvoice active subscriptions
    And there the latest invoice for "customer.three@example.org" will have tax country of "DE"
