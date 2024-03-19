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
    And there are the following tax types:
      | Name             |
      | Digital Goods    |
      | Digital Services |
      | Physical         |

  Scenario: Successfully create product
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a product via the app with the following info
      | Name     | Product Four  |
      | Tax Type | Digital Goods |
    Then the tax type for product "Product Four" is Digital Goods

  Scenario: Digital Service
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a product via the app with the following info
      | Name     | Product Four  |
      | Tax Type | Digital Services |
    Then the tax type for product "Product Four" is Digital Service

  Scenario: Digital Service
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a product via the app with the following info
      | Name     | Product Four  |
      | Tax Type | Physical      |
    Then the tax type for product "Product Four" is Physical

  Scenario: Update product info
    When I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow products exist:
      | Name        | Tax Type      |
      | Product One | Digital Goods |
      | Product Two | Digital Goods |
    When I update the product info via the APP for "Product One":
      | Name     | Product Three |
      | Tax Type | Physical      |
    Then there should be a product with the name "Product Three"
    Then the tax type for product "Product Three" is Physical

