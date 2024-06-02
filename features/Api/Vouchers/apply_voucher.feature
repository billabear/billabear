Feature: Apply voucher

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
      | Product One | 1000   | GBP      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
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

  Scenario: Successfully apply code
    Given I have authenticated to the API
    And the system settings for main currency is "USD"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following vouchers exist:
      | Name        | Type         | Entry Type | Code     | Percentage Value | USD  | GBP | Disabled |
      | Voucher One | Percentage   | Automatic  | n/a      | 25               | n/a  | n/a | true     |
      | Voucher Two | Fixed Credit | Manual     | code_one | n/a              | 1000 | 800 | false     |
    When I apply the voucher code "code_one" to customer "customer.one@example.org"
    Then there should be a record of "Voucher Two" being applied to "customer.one@example.org"
    Then there should be a credit for "customer.one@example.org" for 1000 in the currency "USD"

  Scenario: Successfully apply code - subscription currency
    Given I have authenticated to the API
    And the system settings for main currency is "USD"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following vouchers exist:
      | Name        | Type         | Entry Type | Code     | Percentage Value | USD  | GBP | Disabled |
      | Voucher One | Percentage   | Automatic  | n/a      | 25               | n/a  | n/a | true     |
      | Voucher Two | Fixed Credit | Manual     | code_one | n/a              | 1000 | 800 | false    |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3500         | GBP            | month          | customer.one@example.org |
    When I apply the voucher code "code_one" to customer "customer.one@example.org"
    Then there should be a record of "Voucher Two" being applied to "customer.one@example.org"
    Then there should be a credit for "customer.one@example.org" for 800 in the currency "GBP"

  Scenario: Successfully apply code - subscription currency
    Given I have authenticated to the API
    And the system settings for main currency is "USD"
    And the follow customers exist:
      | Email                    | Country | External Reference | Reference    |
      | customer.one@example.org | DE      | cust_jf9j545       | Customer One |
      | customer.two@example.org | UK      | cust_dfugfdu       | Customer Two |
    And the following vouchers exist:
      | Name        | Type         | Entry Type | Code     | Percentage Value | USD  | GBP | Disabled |
      | Voucher One | Percentage   | Automatic  | n/a      | 25               | n/a  | n/a | true     |
      | Voucher Two | Fixed Credit | Manual     | code_one | n/a              | 1000 | 800 | true     |
    And the following subscriptions exist:
      | Subscription Plan | Price Amount | Price Currency | Price Schedule | Customer                 |
      | Test Plan         | 3500         | GBP            | month          | customer.one@example.org |
    When I apply the voucher code "code_one" to customer "customer.one@example.org"
    Then I should be told there is a validation error with the code
    Then there should not be a record of "Voucher Two" being applied to "customer.one@example.org"