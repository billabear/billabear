Feature: Product Update APP
  In order to keep product data up to date
  As an APP user
  I need to be update a product

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Update product info
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow products exist:
      | Name        | Tax Rate |
      | Product One | 14       |
      | Product Two |          |
    When I update the product info via the APP for "Product One":
      | Name          | Product Three |
    Then there should be a product with the name "Product Three"

  Scenario: Update product info - tax info
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    When I update the product info via the APP for "Product One":
      | Name          | Product Three |
      | Tax Rate      | 34            |
    Then the product "Product Three" should have the tax rate 34
