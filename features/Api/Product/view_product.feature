Feature: View Product
  In order to ensure the data is correct for a product
  As an API user
  I need to be able to view the product

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: View Product
    Given I have authenticated to the API
    And the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    When I use the API to view product "Product One"
    Then I will see the data "name" with value "Product One"