Feature: Create Voucher

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Create Voucher - view
    Given the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | GBP      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I go to the create voucher page
    Then I will see the currency "USD" is available under the voucher currencies
    And I will see the currency "GBP" is available under the voucher currencies

  Scenario: Create Voucher - percentage
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a voucher:
      | Type        | Percentage           |
      | Entry Type  | Automatic            |
      | Value       | 20                   |
      | Entry Event | Expired Card Warning |
      | Name        | Expired Card Bonus   |
    Then there should be a voucher called "Expired Card Bonus" with:
      | Value | 20         |
      | Type  | Percentage |

  Scenario: Create Voucher - fixed amount
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a voucher:
      | Type        | Fixed Amount         |
      | Entry Type  | Automatic            |
      | Entry Event | Expired Card Warning |
      | Name        | Expired Card Bonus   |
      | USD         | 3000                 |
      | GBP         | 2000                 |
    Then there should be a voucher called "Expired Card Bonus" with:
      | Type  | Fixed Amount  |
    And the voucher "Expired Card Bonus" has an amount for the currency "USD" and value 3000
    And the voucher "Expired Card Bonus" has an amount for the currency "GBP" and value 2000

  Scenario: Create Voucher - fixed amount no amounts
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a voucher:
      | Type        | Fixed Amount         |
      | Entry Type  | Automatic            |
      | Entry Event | Expired Card Warning |
      | Name        | Expired Card Bonus   |
    Then there should not be a voucher called "Expired Card Bonus"
    And there will be a validation error for the amounts


  Scenario: Create Voucher - Manual Entry
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a voucher:
      | Type        | Percentage           |
      | Value       | 20                   |
      | Entry Type  | Manual            |
      | Name        | Mailing List Welcome   |
      | Code        | welcome50              |
    Then there should be a voucher called "Mailing List Welcome"
    And the voucher "Mailing List Welcome" has the code "welcome50"

  Scenario: Create Voucher - Manual Entry no code
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a voucher:
      | Type        | Percentage           |
      | Value       | 20                   |
      | Entry Type  | Manual            |
      | Name        | Mailing List Welcome   |
    Then there should not be a voucher called "Mailing List Welcome"
    And there should be a validation error for code

