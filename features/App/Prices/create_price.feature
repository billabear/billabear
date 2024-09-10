Feature: Price Creation
  In order to charge customers for products
  As an APP user
  I need to create a price for a product

  Background:
    Given the following accounts exist:
      | Name        | Email                   | Password  |
      | Sally Brown | sally.brown@example.org | AF@k3P@ss |
      | Tim Brown   | tim.brown@example.org   | AF@k3P@ss |
      | Sally Braun | sally.braun@example.org | AF@k3Pass |
    Given the follow products exist:
      | Name        | External Reference |
      | Product One | prod_jf9j545       |
      | Product Two | prod_jf9j542       |

  Scenario: Create a price
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a price via the app for the product "Product One"
      | Amount    | 1000  |
      | Currency  | USD   |
      | Recurring | false |
    Then there should be a price for "Product One" with the amount 1000

  Scenario: Create a price package
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a price via the app for the product "Product One"
      | Amount    | 1000    |
      | Currency  | USD     |
      | Type      | Package |
      | Units     | 100     |
      | Recurring | True    |
    Then there should be a package price for "Product One" with the amount 1000 and 100 units
    And there should not be a usage package price for "Product One" with the amount 1000 and 100 units

  Scenario: Invalid a price package
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    When I create a price via the app for the product "Product One"
      | Amount    | 1000    |
      | Currency  | USD     |
      | Type      | Package |
      | Recurring | True    |
    Then there should not be a price for "Product One" with the amount 1000

  Scenario: Create a usage price package
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow metrics exist:
      | Name     | Code     | Type    | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Metered | Count              |                      | Real Time |
    When I create a price via the app for the product "Product One"
      | Amount    | 1000     |
      | Currency  | USD      |
      | Type      | Package  |
      | Units     | 100      |
      | Recurring | True     |
      | Usage     | True     |
      | Metric    | Test One |
      | Metric Type | Resettable |
    Then there should be a usage package price for "Product One" with the amount 1000 and 100 units

  Scenario: Invalid usage price package
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And the follow metrics exist:
      | Name     | Code     | Aggregation Method | Aggregation Property | Ingestion |
      | Test One | test_one | Count              |                      | Real Time |
    When I create a price via the app for the product "Product One"
      | Currency    | USD        |
      | Type        | Package    |
      | Units       | 100        |
      | Recurring   | True       |
      | Usage       | True       |
      | Metric      | Test One   |
      | Metric Type | Resettable |
    Then there should not be a usage package price for "Product One" with the amount 1000 and 100 units

  Scenario: Create a tier price
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I configure tiers pricing of:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 11         | 20        | 0          | 1500     |
    When I create a price via the app for the product "Product One"
      | Currency  | USD           |
      | Type      | Tiered Volume |
      | Recurring | True          |
      | Usage     | False         |
    Then there should be a tier volume price for "Product One" with the following tiers:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 11         | 20        | 0          | 1500     |

  Scenario: Invalid tiers
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I configure tiers pricing of:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 5          | 20        | 0          | 1500     |
    When I create a price via the app for the product "Product One"
      | Currency  | USD           |
      | Type      | Tiered Volume |
      | Recurring | True          |
      | Usage     | True          |
    Then there should not be a tier volume price for "Product One"

  Scenario: Invalid tiers 2
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I configure tiers pricing of:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 2          | 20        | 0          | 1500     |
    When I create a price via the app for the product "Product One"
      | Currency  | USD           |
      | Type      | Tiered Volume |
      | Recurring | True          |
      | Usage     | True          |
    Then there should not be a tier volume price for "Product One"

  Scenario: Invalid tiers 3
    Given I have logged in as "sally.brown@example.org" with the password "AF@k3P@ss"
    And I configure tiers pricing of:
      | First Unit | Last Unit | Unit Price | Flat Fee |
      | 1          | 10        | 0          | 1000     |
      | 21         | 30        | 0          | 1500     |
    When I create a price via the app for the product "Product One"
      | Currency  | USD           |
      | Type      | Tiered Volume |
      | Recurring | True          |
      | Usage     | True          |
    Then there should not be a tier volume price for "Product One"
