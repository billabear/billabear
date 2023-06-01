Feature: Create Voucher

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

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
