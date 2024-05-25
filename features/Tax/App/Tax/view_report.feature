Feature: View Tax Report

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    And there are the following tax types:
      | Name           |
      | Digital Goods  |
      | Physical       |
    And the follow products exist:
      | Name        | External Reference | Tax Type      |
      | Product One | prod_jf9j545       | Digital Goods |
      | Product Two | prod_jf9j542       | Digital Goods |
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
      | Email                      | Country | External Reference | Reference      | Billing Type |
      | customer.one@example.org   | DE      | cust_jf9j545       | Customer One   | invoice      |
      | customer.two@example.org   | GB      | cust_dfugfdu       | Customer Two   | card         |
      | customer.three@example.org | GB      | cust_mlklfdu       | Customer Three | card         |
      | customer.four@example.org  | GB      | cust_dkkoadu       | Customer Four  | card         |
      | customer.five@example.org  | GB      | cust_ddsjfu        | Customer Five  | card         |
      | customer.six@example.org   | GB      | cust_jliujoi       | Customer Six   | card         |
    Given the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                   | Next Charge | Status    |
      | Test Plan         | 1000         | USD            | week           | customer.one@example.org   | +3 Minutes  | Active    |
      | Test Plan         | 3000         | USD            | month          | customer.two@example.org   | +3 Minutes  | Active    |
      | Test Two          | 30000        | USD            | year           | customer.three@example.org | +3 Minutes  | Active    |
      | Test Plan         | 1000         | USD            | week           | customer.four@example.org  | +3 Minutes  | Cancelled |
      | Test Plan         | 3000         | USD            | month          | customer.five@example.org  | +10 Minutes | Active    |
      | Test Two          | 30000        | USD            | year           | customer.six@example.org   | +10 Minutes | Active    |

  Scenario:
    Given the following invoices exist:
      | Customer                 | Paid  | Tax Type      |
      | customer.one@example.org | true  | Digital Goods |
      | customer.two@example.org | false | Digital Goods |
    And there is a payments for:
      | Subscription Plan | Customer                  | Amount |
      | Test Plan         | customer.four@example.org | 3500   |
    And I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I generate a receipt for the payment for "customer.four@example.org" for 3500
    When I view the tax report page
    Then I will not see a tax item for "customer.two@example.org"
    But I will see a tax item for "customer.one@example.org"
    But I will see a tax item for "customer.four@example.org"

  Scenario:
    Given the following invoices exist:
      | Customer                  | Paid  | Tax Type      |
      | customer.one@example.org  | true  | Digital Goods |
      | customer.five@example.org | true  | Digital Goods |
      | customer.six@example.org  | false | Digital Goods |
    And there is a payments for:
      | Subscription Plan | Customer                  | Amount |
      | Test Plan         | customer.four@example.org | 3500   |
    And I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I generate a receipt for the payment for "customer.four@example.org" for 3500
    When I view the tax report page
    Then I will see that the country "DE" in list of active countries
    Then I will see that the country "GB" in list of active countries
