Feature: Product Update API
  In order to keep product data up to date
  As an API user
  I need to be update a product

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Update product info
    Given I have authenticated to the API
    And the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    When I update the product info via the API for "Product One":
      | Name          | Product Three |
    Then there should be a product with the name "Product Three"

