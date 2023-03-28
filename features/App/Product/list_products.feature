Feature: List Product
  In order to manage products
  As an APP user
  I need to be see all the products

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: List Products
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    When I use the APP to list product
    Then I should see in the API response the product "Product Two"
    And I should see in the API response the product "Product One"
