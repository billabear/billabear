Feature: Price Creation
  In order to charge customers for products
  As an APP user
  I need to create a price for a product

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    Given the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |

  Scenario: Create a price
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a price via the app for the product "Product One"
      | Amount    | 1000  |
      | Currency  | USD   |
      | Recurring | false |
    Then there should be a price for "Product One" with the amount 1000