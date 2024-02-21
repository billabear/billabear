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
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a product via the app with the following info
      | Name | Product Four |
    Then there should be a product with the name "Product Four"

  Scenario: Successfully create product with tax rate
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a product via the app with the following info
      | Name     | Product Four |
      | Tax Rate | 20           |
    Then the product "Product Four" should have the tax rate 20

  Scenario: See the tax types in the database
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And there are the following tax types:
      | Name     |
      | Digital  |
      | Physical |
    When I go to create a product
    Then I will see a tax type in the tax type dropdown called "Digital"
    And I will see a tax type in the tax type dropdown called "Physical"