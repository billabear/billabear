Feature: Disable Voucher

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario:
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the following vouchers exist:
      | Name        | Type         | Entry Type | Code     | Percentage Value | USD  | GBP |
      | Voucher One | Percentage   | Automatic  | n/a      | 25               | n/a  | n/a |
      | Voucher Two | Fixed Credit | Manual     | code_one | n/a              | 1000 | 800 |
    When I disable the voucher "Voucher One"
    Then the voucher "Voucher One" will be disabled
    And the voucher "Voucher Two" will be enabled