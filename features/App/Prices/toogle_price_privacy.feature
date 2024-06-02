Feature: Toogle prices privacy

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Make private price
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    When I use the APP to make a price from "Product One" for 1000 per "week" private
    Then the price for "Product One" with the amount 1000 should be private

  Scenario: Make public price
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |
    When I use the APP to make a price from "Product One" for 30000 per "year" public
    Then the price for "Product One" with the amount 30000 should be public
