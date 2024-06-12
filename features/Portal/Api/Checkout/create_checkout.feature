Feature: Create Checkout

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
    And the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
    And the follow prices exist:
      | Product     | Amount | Currency | Recurring | Schedule | Public |
      | Product One | 1000   | USD      | true      | week     | true   |
    And the following features exist:
      | Name          | Code          | Description     |
      | Feature One   | feature_one   | A dummy feature |
      | Feature Two   | feature_two   | A dummy feature |
      | Feature Three | feature_three | A dummy feature |
    Given a Subscription Plan exists for product "Product One" with a feature "Feature One" and a limit for "Feature Two" with a limit of 10 and price 1000 in "USD" with:
      | Name       | Test Two |
      | Public     | True     |
      | Per Seat   | False    |
      | User Count | 10       |
    And the follow brands exist:
      | Name    | Code    | Email               |
      | Example | example | example@example.org |
    And there are the following tax types:
      | Name          |
      | Digital Goods |
      | Physical      |

  Scenario: Create Checkout
    Given I have authenticated to the API
    And I start creating a checkout called "Test"
    And I add a subscription to "Test Two" at 1000 in "USD" per "week" to checkout
    And I add a one-off fee of 3000 in "USD" for "Setup Fee"
    And I set the brand for the checkout as "Example"
    And I set the checkout to be permanent
    When I create the checkout via the API
    Then there should be a permanent checkout called "Test"
    And the checkout "Test" should have a payment amount of 4000 "USD"

  Scenario: Create Checkout
    Given I have authenticated to the API
    And I start creating a checkout called "Test"
    And I add a subscription to "Test Two" at 1000 in "USD" per "week" to checkout
    And I add a one-off fee of 3000 in "USD" for "Setup Fee"
    And I set the brand for the checkout as "Example"
    And I set the checkout to be permanent
    When I create the checkout via the API
    Then there should be a permanent checkout called "Test"
    And the checkout "Test" should have a payment amount of 4000 "USD"
