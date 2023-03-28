Feature: Create Product
  In order to bill for products
  As an APP user
  I need to be create a product

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |

  Scenario: Successfully create product
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a product via the app with the following info
      | Name | Product Four |
    Then there should be a product with the name "Product Four"
