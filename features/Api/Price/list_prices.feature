Feature: Price List
  In order to ensure the price data is correct for a product
  As an API user
  I need to be able to view the prices for a product

  Background:
    Given the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
      | Product One | 3000   | USD      | true      | month    | true   |
      | Product One | 30000  | USD      | true      | year     | false  |

  Scenario: Create a product
    Given I have authenticated to the API
    When I fetch all prices for the product "Product One" via API
    Then I should see in the API response with only 3 result in the data set
    And there should be a price for 1000 in the data set