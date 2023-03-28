Feature: Price Creation
  In order to charge customers for products
  As an API user
  I need to create a price for a product

  Background:
    Given the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |

  Scenario: Create a price
    Given I have authenticated to the API
    When I create a price for the product "Product One"
      | Amount    | 1000  |
      | Currency  | USD   |
      | Recurring | false |
    Then there should be a price for "Product One" with the amount 1000