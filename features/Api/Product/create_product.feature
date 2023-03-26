Feature: Product Creation
  In order to charge customers for products
  As an API user
  I need to create product

  Scenario: Create a product
    Given I have authenticated to the API
    When I create a product with the following info
      | Name | A test Product |
    Then there should be a product with the name "A test Product"
