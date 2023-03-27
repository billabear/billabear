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
