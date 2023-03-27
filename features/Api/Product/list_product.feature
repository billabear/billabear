Feature: Product List
  In order to keep track of products
  As an API user
  I need to be see what product are

  Scenario: Raw list
    Given I have authenticated to the API
    And the follow products exist:
      | Name        |
      | Product One |
      | Product Two |
    When I use the API to list product
    Then I should see in the API response the product "Product Two"
    And I should see in the API response the product "Product One"

  Scenario: Limited to one
    Given I have authenticated to the API
    And the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |
    When I use the API to list products with parameter "name" with value "One"
    Then I should see in the API response with only 1 result in the data set
    And I should see in the API response the product "Product One"

  Scenario: Limited to one
    Given I have authenticated to the API
    And the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |
    When I use the API to list products with parameter "external_reference" with value "prod_jf9j542"
    Then I should see in the API response with only 1 result in the data set
    And I should see in the API response the product "Product Two"